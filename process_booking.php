<?php
/**
 * ZAMAHI Luxury Catering - Process Booking
 * Handles form submission, DB insert, invoice generation, email
 */

// Start output buffering to prevent any unexpected output
ob_start();

// Error handling - return JSON on any error
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $errstr
    ]);
    exit;
});

set_exception_handler(function($exception) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $exception->getMessage()
    ]);
    exit;
});

header('Content-Type: application/json');

// Get raw input for debugging
$rawInput = file_get_contents('php://input');
$postData = $_POST;

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

try {
    // CSRF check
    if (empty($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        throw new Exception('Invalid or missing security token. Please refresh the page and try again.');
    }

    // Sanitize inputs
    $customerName  = sanitize($_POST['customer_name'] ?? '');
    $customerEmail = sanitizeEmail($_POST['customer_email'] ?? '');
    $customerPhone = sanitize($_POST['customer_phone'] ?? '');
    $eventCategory = sanitize($_POST['event_category'] ?? '');
    $eventCategoryOther = sanitize($_POST['event_category_other'] ?? '');
    $eventSubCategory = sanitize($_POST['event_type'] ?? '');
    $eventTypeOther = sanitize($_POST['event_type_other'] ?? '');
    
    // Determine final event category
    $finalEventCategory = '';
    if ($eventCategory === 'Other' && $eventCategoryOther) {
        $finalEventCategory = $eventCategoryOther;
    } elseif ($eventCategory) {
        $finalEventCategory = $eventCategory;
    }
    
    // Determine final event type
    $eventType = '';
    if ($eventSubCategory === 'Other' && $eventTypeOther) {
        $eventType = $eventTypeOther;
    } elseif ($eventSubCategory) {
        $eventType = $eventSubCategory;
    }
    
    $eventDate     = sanitize($_POST['event_date'] ?? '');
    $eventTimeRaw  = $_POST['event_time'] ?? '';
    
    // Validate and normalize time to 24-hour format (HH:MM)
    $eventTime = '';
    if ($eventTimeRaw) {
        // Accept 24-hour format HH:MM or H:MM
        if (preg_match('/^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $eventTimeRaw)) {
            // Normalize to 2-digit hour
            $parts = explode(':', $eventTimeRaw);
            $eventTime = str_pad($parts[0], 2, '0', STR_PAD_LEFT) . ':' . $parts[1];
        } else {
            throw new Exception('Invalid time format. Please use 24-hour format (e.g., 14:00, 19:30).');
        }
    }
    
    $guestCount    = max(1, (int)($_POST['guest_count'] ?? 1));
    $kidsCount     = max(0, (int)($_POST['kids_count'] ?? 0));
    $kidsFree      = max(0, (int)($_POST['kids_free_count'] ?? 0));
    $indoorOutdoor = in_array($_POST['indoor_outdoor'] ?? '', ['indoor', 'outdoor']) ? $_POST['indoor_outdoor'] : 'indoor';
    $address       = sanitize($_POST['address'] ?? '');
    $fullAddress   = sanitize($_POST['full_address'] ?? $address);
    $postcode      = sanitize($_POST['postcode'] ?? '');
    $latitude      = !empty($_POST['latitude']) ? floatval($_POST['latitude']) : null;
    $longitude     = !empty($_POST['longitude']) ? floatval($_POST['longitude']) : null;
    $instructions  = sanitize($_POST['instructions'] ?? '');
    $paymentMethod = in_array($_POST['payment_method'] ?? '', ['cash_on_delivery', 'cash_pickup', 'advance_payment'])
                     ? $_POST['payment_method'] : 'cash_on_delivery';
    $promoCode     = sanitize($_POST['promo_code'] ?? '');
    $spiceLevel    = sanitize($_POST['spice_level'] ?? 'medium');
    
    // Dish type (single or multiple)
    $dishType = in_array($_POST['dish_type'] ?? '', ['single', 'multiple']) ? $_POST['dish_type'] : 'single';
    
    // Protein types (for multiple dishes)
    $proteinTypes = [];
    if (isset($_POST['protein_types']) && is_array($_POST['protein_types'])) {
        $proteinTypes = array_map('sanitize', $_POST['protein_types']);
    }
    
    // Rice, Bread, Salad, Sauce items
    $riceItems = [];
    if (isset($_POST['rice_items']) && is_array($_POST['rice_items'])) {
        $riceItems = array_map('sanitize', $_POST['rice_items']);
    }
    $breadItems = [];
    if (isset($_POST['bread_items']) && is_array($_POST['bread_items'])) {
        $breadItems = array_map('sanitize', $_POST['bread_items']);
    }
    $saladItems = [];
    if (isset($_POST['salad_items']) && is_array($_POST['salad_items'])) {
        $saladItems = array_map('sanitize', $_POST['salad_items']);
    }
    $sauceItems = [];
    if (isset($_POST['sauce_items']) && is_array($_POST['sauce_items'])) {
        $sauceItems = array_map('sanitize', $_POST['sauce_items']);
    }

    // Validate required fields
    $requiredFields = [];
    if (!$customerName) $requiredFields[] = 'customer_name';
    if (!$customerEmail) $requiredFields[] = 'customer_email';
    if (!$customerPhone) $requiredFields[] = 'customer_phone';
    if (!$finalEventCategory) $requiredFields[] = 'event_category';
    if (!$eventType) $requiredFields[] = 'event_type';
    if (!$eventDate) $requiredFields[] = 'event_date';
    if (!$address) $requiredFields[] = 'address';
    if (!$postcode) $requiredFields[] = 'postcode';
    
    if (!empty($requiredFields)) {
        throw new Exception('Please fill in all required fields. Missing: ' . implode(', ', $requiredFields));
    }
    
    // Validate protein selection based on dish type
    if ($dishType === 'single') {
        $proteinType = sanitize($_POST['protein_type'] ?? '');
        if (!$proteinType) {
            throw new Exception('Please select a protein type.');
        }
    } else {
        if (empty($proteinTypes)) {
            throw new Exception('Please select at least one protein type.');
        }
    }

    if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Please enter a valid email address.');
    }

    // Server-side price calculation
    $kidsPaid = $kidsCount - $kidsFree;
    $billableGuests = $guestCount + max(0, $kidsPaid);
    $perHead = BASE_PRICE_PER_HEAD;
    $subtotal = $billableGuests * $perHead;

    // Discount
    $discount = 0;
    if ($guestCount >= 50) {
        $discount = $subtotal * DISCOUNT_50_GUESTS;
    }

    // Allergy surcharges
    $allergyCharges = 0;
    $allergyTypes = $_POST['allergy_types'] ?? [];
    $allergyCounts = $_POST['allergy_counts'] ?? [];
    $allergyData = [];
    if (is_array($allergyTypes)) {
        foreach ($allergyTypes as $type) {
            $count = max(0, (int)($allergyCounts[$type] ?? 0));
            if ($count > 0) {
                $charge = $count * ALLERGY_SURCHARGE;
                $allergyCharges += $charge;
                $allergyData[] = ['type' => $type, 'count' => $count, 'charge' => $charge];
            }
        }
    }

    // Starter add-ons
    $starterIds = $_POST['starters'] ?? [];
    $starterCharges = 0;
    $starterData = [];
    if (is_array($starterIds) && !empty($starterIds)) {
        $placeholders = implode(',', array_fill(0, count($starterIds), '?'));
        $sStmt = $pdo->prepare("SELECT id, name, price FROM menu_items WHERE id IN ($placeholders) AND is_addon = 1");
        $sStmt->execute($starterIds);
        $starters = $sStmt->fetchAll();
        foreach ($starters as $s) {
            $cost = $s['price'] * $billableGuests;
            $starterCharges += $cost;
            $starterData[] = ['id' => $s['id'], 'name' => $s['name'], 'price' => $s['price']];
        }
    }

    // Drinks charges
    $drinkIds = $_POST['drinks'] ?? [];
    $drinksCharge = 0;
    $drinkData = [];
    if (is_array($drinkIds) && !empty($drinkIds)) {
        $placeholders = implode(',', array_fill(0, count($drinkIds), '?'));
        $dStmt = $pdo->prepare("SELECT id, name, price FROM menu_items WHERE id IN ($placeholders)");
        $dStmt->execute($drinkIds);
        $drinks = $dStmt->fetchAll();
        foreach ($drinks as $d) {
            $cost = $d['price'] * $billableGuests;
            $drinksCharge += $cost;
            $drinkData[] = ['id' => $d['id'], 'name' => $d['name'], 'price' => $d['price']];
        }
    }

    // Additional services
    $serviceKeys = $_POST['services'] ?? [];
    $servicesTotal = 0;
    $servicesData = [];
    $allServices = json_decode(SERVICES_PRICING, true);
    if (is_array($serviceKeys)) {
        foreach ($serviceKeys as $key) {
            if (isset($allServices[$key])) {
                $price = $allServices[$key]['price'];
                // Free waiter for 150+ guests
                if ($key === 'waiter_hire' && $guestCount >= FREE_WAITER_THRESHOLD) {
                    $price = 0;
                }
                $servicesTotal += $price;
                $servicesData[] = ['key' => $key, 'name' => $allServices[$key]['name'], 'price' => $price];
            }
        }
    }

    // Delivery
    $deliveryCharge = DELIVERY_CHARGE;
    if ($guestCount >= FREE_DELIVERY_THRESHOLD) {
        $deliveryCharge = 0;
    }

    // Promo discount
    $promoDiscount = 0;
    if ($promoCode) {
        $pStmt = $pdo->prepare("SELECT * FROM offers WHERE code = ? AND is_active = 1 AND (valid_from IS NULL OR valid_from <= CURDATE()) AND (valid_to IS NULL OR valid_to >= CURDATE()) AND (max_uses IS NULL OR used_count < max_uses)");
        $pStmt->execute([$promoCode]);
        $promo = $pStmt->fetch();
        if ($promo) {
            if ($promo['type'] === 'percentage') {
                $promoDiscount = $subtotal * ($promo['value'] / 100);
            } else {
                $promoDiscount = $promo['value'];
            }
            // Update used count
            $pdo->prepare("UPDATE offers SET used_count = used_count + 1 WHERE id = ?")->execute([$promo['id']]);
        }
    }

    $totalDiscount = $discount + $promoDiscount;
    $preVat = ($subtotal - $totalDiscount) + $allergyCharges + $starterCharges + $drinksCharge + $servicesTotal + $deliveryCharge;
    $vat = round($preVat * VAT_RATE, 2);
    $grandTotal = round($preVat + $vat, 2);

    // Generate ref
    $refNumber = generateBookingRef();

    // Begin transaction
    $pdo->beginTransaction();

    // Insert booking
    $bookingStmt = $pdo->prepare("
        INSERT INTO bookings (ref_number, customer_name, customer_email, customer_phone, event_category, event_sub_category, event_type, event_date, event_time, 
            guest_count, kids_count, kids_free, indoor_outdoor, address, postcode, lat, lng, instructions,
            per_head_cost, subtotal, discount, allergy_charges, services_total, delivery_charge, vat, grand_total,
            payment_method, promo_code, promo_discount, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    $bookingStmt->execute([
        $refNumber, $customerName, $customerEmail, $customerPhone, $finalEventCategory, $eventSubCategory, $eventType, $eventDate, $eventTime,
        $guestCount, $kidsCount, $kidsFree, $indoorOutdoor, $address, $postcode, $latitude, $longitude, $instructions,
        $perHead, round($subtotal, 2), round($totalDiscount, 2), round($allergyCharges, 2),
        round($servicesTotal, 2), round($deliveryCharge, 2), $vat, $grandTotal,
        $paymentMethod, $promoCode ?: null, round($promoDiscount, 2)
    ]);
    $bookingId = $pdo->lastInsertId();

    // Insert menu selections
    $proteinType = sanitize($_POST['protein_type'] ?? '');
    $proteinDishes = $_POST['protein_dishes'] ?? [];
    $multiDishes = $_POST['multi_dishes'] ?? [];
    $menuItemIds = $_POST['menu_items'] ?? [];
    
    // Cache of valid menu item IDs for quick lookup
    $validMenuItemIds = [];
    
    // Helper function to validate menu_item_id exists in database
    function getValidMenuItemId($pdo, $id, &$cache) {
        $id = (int)$id;
        if ($id <= 0) {
            return null;
        }
        
        // Use cache if available
        if (isset($cache[$id])) {
            return $cache[$id];
        }
        
        // Query database
        $stmt = $pdo->prepare("SELECT id FROM menu_items WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $item = $stmt->fetch();
        
        $validId = $item ? $item['id'] : null;
        $cache[$id] = $validId;
        
        return $validId;
    }
    
    // Helper function to validate and get menu item ID by name
    function findOrCreateMenuItem($pdo, $name, $category, $price = 0, &$cache = []) {
        $name = sanitize(trim($name));
        
        if (empty($name)) {
            return null;
        }
        
        // Try to find existing by exact name match
        $stmt = $pdo->prepare("SELECT id, name, category, price FROM menu_items WHERE name = ? LIMIT 1");
        $stmt->execute([$name]);
        $item = $stmt->fetch();
        
        if ($item) {
            $cache[$item['id']] = $item['id'];
            return $item;
        }
        
        // Try case-insensitive match
        $stmt = $pdo->prepare("SELECT id, name, category, price FROM menu_items WHERE LOWER(name) = LOWER(?) LIMIT 1");
        $stmt->execute([$name]);
        $item = $stmt->fetch();
        
        if ($item) {
            $cache[$item['id']] = $item['id'];
            return $item;
        }
        
        // Create new if not found
        try {
            $insert = $pdo->prepare("INSERT INTO menu_items (name, category, price, is_active) VALUES (?, ?, ?, 1)");
            $insert->execute([$name, $category, $price]);
            
            $newId = (int)$pdo->lastInsertId();
            $cache[$newId] = $newId;
            
            return [
                'id' => $newId,
                'name' => $name,
                'category' => $category,
                'price' => $price
            ];
        } catch (Exception $e) {
            error_log("Failed to create menu item: " . $e->getMessage());
            return null;
        }
    }

    // Protein dishes (single dish mode)
    if ($dishType === 'single' && is_array($proteinDishes)) {
        foreach ($proteinDishes as $dishName) {
            $dishName = sanitize($dishName);
            $dish = findOrCreateMenuItem($pdo, $dishName, 'protein', 0, $validMenuItemIds);
            if ($dish && $dish['id']) {
                $pdo->prepare("INSERT INTO booking_menu (booking_id, menu_item_id, item_name, item_category, quantity, price, spice_level) VALUES (?, ?, ?, ?, 1, ?, ?)")
                    ->execute([$bookingId, $dish['id'], $dish['name'], $dish['category'], $dish['price'], $spiceLevel]);
            }
        }
    }
    
    // Multiple dishes mode - save protein types and their dishes
    if ($dishType === 'multiple') {
        // Save protein types
        foreach ($proteinTypes as $prot) {
            $protItem = findOrCreateMenuItem($pdo, $prot, 'protein', 0, $validMenuItemIds);
            if ($protItem && $protItem['id']) {
                $pdo->prepare("INSERT INTO booking_menu (booking_id, menu_item_id, item_name, item_category, quantity, price, spice_level) VALUES (?, ?, ?, 'protein_type', 1, 0, ?)")
                    ->execute([$bookingId, $protItem['id'], $prot, $spiceLevel]);
            }
        }
        
        // Save multiple dishes
        if (is_array($multiDishes)) {
            foreach ($multiDishes as $protein => $dishes) {
                if (is_array($dishes)) {
                    foreach ($dishes as $dishName) {
                        $dishName = sanitize($dishName);
                        $dish = findOrCreateMenuItem($pdo, $dishName, 'protein', 0, $validMenuItemIds);
                        if ($dish && $dish['id']) {
                            $pdo->prepare("INSERT INTO booking_menu (booking_id, menu_item_id, item_name, item_category, quantity, price, spice_level) VALUES (?, ?, ?, ?, 1, ?, ?)")
                                ->execute([$bookingId, $dish['id'], $dish['name'], $dish['category'], $dish['price'], $spiceLevel]);
                        }
                    }
                }
            }
        }
    }
    
    // Other menu items (by ID) - validate IDs exist
    if (is_array($menuItemIds)) {
        foreach ($menuItemIds as $itemId) {
            $validId = getValidMenuItemId($pdo, $itemId, $validMenuItemIds);
            if ($validId) {
                $mStmt = $pdo->prepare("SELECT id, name, category, price FROM menu_items WHERE id = ?");
                $mStmt->execute([$validId]);
                $item = $mStmt->fetch();
                if ($item) {
                    $pdo->prepare("INSERT INTO booking_menu (booking_id, menu_item_id, item_name, item_category, quantity, price) VALUES (?, ?, ?, ?, 1, ?)")
                        ->execute([$bookingId, $item['id'], $item['name'], $item['category'], $item['price']]);
                }
            }
        }
    }
    
    // Rice items (store item_name only - no FK)
    foreach ($riceItems as $rice) {
        $riceName = sanitize($rice);
        if ($riceName) {
            $pdo->prepare("INSERT INTO booking_menu (booking_id, item_name, item_category, quantity, price) VALUES (?, ?, 'rice', 1, 0)")
                ->execute([$bookingId, $riceName]);
        }
    }
    
    // Bread items (store item_name only - no FK)
    foreach ($breadItems as $bread) {
        $breadName = sanitize($bread);
        if ($breadName) {
            $pdo->prepare("INSERT INTO booking_menu (booking_id, item_name, item_category, quantity, price) VALUES (?, ?, 'bread', 1, 0)")
                ->execute([$bookingId, $breadName]);
        }
    }
    
    // Salad items (store item_name only - no FK)
    foreach ($saladItems as $salad) {
        $saladName = sanitize($salad);
        if ($saladName) {
            $pdo->prepare("INSERT INTO booking_menu (booking_id, item_name, item_category, quantity, price) VALUES (?, ?, 'salad', 1, 0)")
                ->execute([$bookingId, $saladName]);
        }
    }
    
    // Sauce items (store item_name only - no FK)
    foreach ($sauceItems as $sauce) {
        $sauceName = sanitize($sauce);
        if ($sauceName) {
            $pdo->prepare("INSERT INTO booking_menu (booking_id, item_name, item_category, quantity, price) VALUES (?, ?, 'sauce', 1, 0)")
                ->execute([$bookingId, $sauceName]);
        }
    }

    // Starters - validate ID exists
    foreach ($starterData as $s) {
        $validId = getValidMenuItemId($pdo, $s['id'] ?? 0, $validMenuItemIds);
        if ($validId && !empty($s['name'])) {
            $pdo->prepare("INSERT INTO booking_menu (booking_id, menu_item_id, item_name, item_category, quantity, price) VALUES (?, ?, ?, 'starters', ?, ?)")
                ->execute([$bookingId, $validId, $s['name'], $billableGuests, $s['price']]);
        }
    }

    // Drinks - validate ID exists
    foreach ($drinkData as $d) {
        $validId = getValidMenuItemId($pdo, $d['id'] ?? 0, $validMenuItemIds);
        if ($validId && !empty($d['name'])) {
            $pdo->prepare("INSERT INTO booking_menu (booking_id, menu_item_id, item_name, item_category, quantity, price) VALUES (?, ?, ?, 'drinks', ?, ?)")
                ->execute([$bookingId, $validId, $d['name'], $billableGuests, $d['price']]);
        }
    }

    // Allergies
    foreach ($allergyData as $a) {
        $pdo->prepare("INSERT INTO booking_allergies (booking_id, allergy_type, guest_count, extra_charge) VALUES (?, ?, ?, ?)")
            ->execute([$bookingId, $a['type'], $a['count'], $a['charge']]);
    }

    // Services
    foreach ($servicesData as $s) {
        $pdo->prepare("INSERT INTO booking_services (booking_id, service_name, price) VALUES (?, ?, ?)")
            ->execute([$bookingId, $s['name'], $s['price']]);
    }

    $pdo->commit();

    // Generate PDF invoice (if TCPDF available)
    $invoicePath = null;
    if (file_exists(__DIR__ . '/generate_invoice.php')) {
        try {
            require_once __DIR__ . '/generate_invoice.php';
            $invoicePath = generateInvoice($pdo, $bookingId);
            if ($invoicePath) {
                $pdo->prepare("UPDATE bookings SET invoice_path = ? WHERE id = ?")->execute([$invoicePath, $bookingId]);
            }
        } catch (Exception $e) {
            error_log('Invoice generation failed: ' . $e->getMessage());
        }
    }

    // Send email (if PHPMailer available)
    if (file_exists(__DIR__ . '/send_email.php')) {
        try {
            require_once __DIR__ . '/send_email.php';
            if (function_exists('sendBookingConfirmation')) {
                sendBookingConfirmation($pdo, $bookingId, $invoicePath);
            }
        } catch (\Exception $emailEx) {
            error_log('Email sending failed: ' . $emailEx->getMessage());
            // Do not throw, allow booking to succeed
        } catch (\Error $emailErr) {
            error_log('Email sending error: ' . $emailErr->getMessage());
        }
    }

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'ref_number' => $refNumber,
        'message' => 'Booking created successfully!'
    ]);

} catch (Exception $e) {
    ob_end_clean();
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
