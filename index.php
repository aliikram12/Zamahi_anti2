<?php
/**
 * ZAMAHI Luxury Catering - Main Page
 * Single-page layout with multi-step booking form
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Fetch active menu items
$menuStmt = $pdo->query("SELECT * FROM menu_items WHERE is_active = 1 ORDER BY category, id");
$menuItems = $menuStmt->fetchAll();
$menuByCategory = [];
foreach ($menuItems as $item) {
    $menuByCategory[$item['category']][] = $item;
}

// Fetch active testimonials
$testStmt = $pdo->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY created_at DESC LIMIT 10");
$testimonials = $testStmt->fetchAll();

// Fetch gallery
$galleryStmt = $pdo->query("SELECT * FROM gallery WHERE is_active = 1 ORDER BY created_at DESC LIMIT 20");
$galleryItems = $galleryStmt->fetchAll();

// Services pricing
$services = json_decode(SERVICES_PRICING, true);

// Check summer season
$isSummer = isSummerSeason();

// CSRF token
$csrfToken = generateCsrfToken();
?>
<?php include __DIR__ . '/includes/header.php'; ?>

    <!-- Summer Banner -->
    <div class="summer-banner <?= $isSummer ? 'active' : '' ?>">
        <i class="fas fa-sun"></i> SUMMER SPECIAL: Complimentary Mojito Mocktail with every booking!
    </div>

    <!-- ═══════════════ HERO ═══════════════ -->
    <section class="hero" id="hero">
        <div class="hero-bg" style="background-image: url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1920&h=1080&fit=crop&crop=center&auto=format&q=90')"></div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-badge">Est. London, UK</div>
            <h1>Luxury Catering for<br><span>Unforgettable Events</span></h1>
            <p class="hero-tagline">Luxury Catering. Exceptional Events.</p>
            <div class="hero-btns">
                <a href="#menu-booking" class="btn btn-gold"><i class="fas fa-calendar-check"></i> Book Your Event</a>
                <a href="#events" class="btn btn-outline"><i class="fas fa-utensils"></i> View Menu</a>
            </div>
        </div>
    </section>

    <!-- ═══════════════ ABOUT ═══════════════ -->
    <section class="about" id="about">
        <div class="container">
            <div class="about-grid">
                <div class="about-text fade-in">
                    <span class="section-label">About Us</span>
                    <h2 class="section-title">Culinary Excellence, Delivered with Grace</h2>
                    <div class="gold-line" style="margin-left:0;"></div>
                    <p>ZAMAHI Luxury Catering delivers premium culinary experiences across the United Kingdom. We specialise in weddings, corporate events, and private celebrations, offering fully customisable menus crafted by expert chefs.</p>
                    <div class="about-stats">
                        <div class="stat-item">
                            <div class="stat-number">500+</div>
                            <div class="stat-label">Events Catered</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">50K+</div>
                            <div class="stat-label">Guests Served</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">100%</div>
                            <div class="stat-label">Satisfaction</div>
                        </div>
                    </div>
                </div>
                <div class="about-image fade-in">
                    <img src="https://images.unsplash.com/photo-1551782450-17144efb5723?w=600&h=400&fit=crop&crop=center&auto=format&q=90" alt="Luxury catering presentation with elegant food plating" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════ EVENTS ═══════════════ -->
    <section class="events" id="events">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-label">What We Cater</span>
                <h2 class="section-title">Events We Specialise In</h2>
                <div class="gold-line"></div>
                <p class="section-subtitle">From intimate celebrations to grand affairs, we bring culinary excellence to every occasion.</p>
            </div>
            <div class="events-grid">
                <?php
                $events = [
                    ['icon' => 'fa-ring',    'name' => 'Weddings',          'desc' => 'Bespoke wedding catering for your perfect day'],
                    ['icon' => 'fa-horse-head',       'name' => 'Barat',             'desc' => 'Grand Barat celebrations with premium service'],
                    ['icon' => 'fa-heart',            'name' => 'Walima',            'desc' => 'Elegant Walima reception catering'],
                    ['icon' => 'fa-hand-sparkles',    'name' => 'Mehndi',            'desc' => 'Vibrant Mehndi night culinary experience'],
                    ['icon' => 'fa-mosque',           'name' => 'Nikah',             'desc' => 'Refined catering for Nikah ceremonies'],
                    ['icon' => 'fa-cake-candles',     'name' => 'Birthday Party',    'desc' => 'Memorable birthday celebrations'],
                    ['icon' => 'fa-water-ladder',     'name' => 'Pool Party',        'desc' => 'Stylish poolside catering & BBQ'],
                    ['icon' => 'fa-ring',             'name' => 'Engagement',        'desc' => 'Celebrate your engagement in style'],
                    ['icon' => 'fa-baby',             'name' => 'Baby Shower',       'desc' => 'Delightful baby shower spreads'],
                    ['icon' => 'fa-building',         'name' => 'Corporate Events',  'desc' => 'Professional corporate catering'],
                    ['icon' => 'fa-chalkboard-user',  'name' => 'Conferences',       'desc' => 'Conference & seminar catering'],
                    ['icon' => 'fa-rocket',           'name' => 'Product Launch',    'desc' => 'Make your launch unforgettable'],
                    ['icon' => 'fa-briefcase',        'name' => 'Office Party',      'desc' => 'Elevated office celebrations'],
                    ['icon' => 'fa-hand-holding-heart','name' => 'Charity Events',   'desc' => 'Catering for charitable occasions'],
                    ['icon' => 'fa-tv',               'name' => 'Sports Screening',  'desc' => 'Game day catering with screens'],
                    ['icon' => 'fa-kitchen-set',         'name' => 'Private Chef',      'desc' => 'Exclusive private chef experience'],
                    ['icon' => 'fa-dove',             'name' => 'Funeral Reception',  'desc' => 'Respectful reception catering'],
                    ['icon' => 'fa-champagne-glasses','name' => 'Anniversary',       'desc' => 'Celebrate milestones together'],
                ];
                foreach ($events as $event):
                ?>
                <div class="event-card fade-in" data-event="<?= htmlspecialchars($event['name']) ?>">
                    <div class="event-icon"><i class="fas <?= $event['icon'] ?>"></i></div>
                    <h3><?= htmlspecialchars($event['name']) ?></h3>
                    <p><?= htmlspecialchars($event['desc']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════ SERVICES ═══════════════ -->
    <section class="services" id="services">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-label">Our Services</span>
                <h2 class="section-title">Premium Event Services</h2>
                <div class="gold-line"></div>
                <p class="section-subtitle">Elevate your event with our bespoke add-on services, each designed to deliver an exceptional experience.</p>
            </div>
            <div class="services-grid">
                <?php
                $serviceIcons = [
                    'waiter_hire'      => 'fa-user-tie',
                    'security_staff'   => 'fa-shield-halved',
                    'live_cooking'     => 'fa-fire-burner',
                    'bbq_setup'        => 'fa-fire',
                    'event_decoration' => 'fa-wand-magic-sparkles',
                    'screens'          => 'fa-tv',
                ];
                $serviceDescriptions = [
                    'waiter_hire'      => 'Professional, uniformed waiters to serve your guests with elegance and precision throughout the event.',
                    'security_staff'   => 'Trained security personnel ensuring the safety and smooth operation of your event.',
                    'live_cooking'     => 'Interactive cooking stations where our chefs prepare dishes right before your guests.',
                    'bbq_setup'        => 'Complete BBQ equipment and setup, perfect for outdoor events and garden celebrations.',
                    'event_decoration' => 'Bespoke decoration services to transform your venue into a breathtaking space.',
                    'screens'          => 'Large-screen setups for sports screenings, presentations, and corporate events.',
                ];
                foreach ($services as $key => $svc):
                ?>
                <div class="service-card fade-in">
                    <div class="service-icon"><i class="fas <?= $serviceIcons[$key] ?? 'fa-concierge-bell' ?>"></i></div>
                    <h3><?= htmlspecialchars($svc['name']) ?></h3>
                    <p><?= $serviceDescriptions[$key] ?? 'Premium service to enhance your event experience.' ?></p>
                    <div class="service-price">From <?= formatCurrency($svc['price']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════ MENU & BOOKING (Multi-Step) ═══════════════ -->
    <section class="booking-section" id="menu-booking">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-label">Reserve Your Experience</span>
                <h2 class="section-title">Book Your Event</h2>
                <div class="gold-line"></div>
                <p class="section-subtitle">Complete the steps below to customise your perfect catering experience.</p>
            </div>

            <!-- Progress Bar -->
            <div class="booking-progress" id="bookingProgress">
                <div class="progress-step active" data-step="1">
                    <span class="step-num">1</span>
                    <span class="step-label">Event</span>
                </div>
                <div class="progress-connector"></div>
                <div class="progress-step" data-step="2">
                    <span class="step-num">2</span>
                    <span class="step-label">Menu</span>
                </div>
                <div class="progress-connector"></div>
                <div class="progress-step" data-step="3">
                    <span class="step-num">3</span>
                    <span class="step-label">Guests</span>
                </div>
                <div class="progress-connector"></div>
                <div class="progress-step" data-step="4">
                    <span class="step-num">4</span>
                    <span class="step-label">Services</span>
                </div>
                <div class="progress-connector"></div>
                <div class="progress-step" data-step="5">
                    <span class="step-num">5</span>
                    <span class="step-label">Location</span>
                </div>
                <div class="progress-connector"></div>
                <div class="progress-step" data-step="6">
                    <span class="step-num">6</span>
                    <span class="step-label">Summary</span>
                </div>
            </div>

            <!-- Booking Form -->
            <form id="bookingForm" class="booking-form-container" novalidate onsubmit="return false;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="dish_type" id="dishTypeInput" value="single">

                <!-- ════ STEP 1: Event Details ════ -->
                <div class="form-step active" id="step1">
                    <h3 style="color:var(--gold);margin-bottom:8px;">Event Details</h3>
                    <p style="color:var(--mid-grey);font-size:0.9rem;margin-bottom:32px;">Tell us about your event so we can tailor the perfect experience.</p>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Your Name <span class="required">*</span></label>
                            <input type="text" name="customer_name" class="form-control" placeholder="Full name" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address <span class="required">*</span></label>
                            <input type="email" name="customer_email" class="form-control" placeholder="your@email.com" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Phone Number <span class="required">*</span></label>
                        <input type="tel" name="customer_phone" class="form-control" placeholder="+44 7XXX XXX XXX" required>
                    </div>
                    <div class="form-group">
                        <label>Event Category <span class="required">*</span></label>
                        <select name="event_category" id="eventCategory" class="form-control" required onchange="handleEventCategoryChange()">
                            <option value="">Select event category</option>
                            <option value="Wedding">Wedding</option>
                            <option value="Parties & Celebrations">Parties</option>
                            <option value="Events">Events</option>
                            <option value="Other">Other</option>
                        </select>
                        <div id="otherCategoryContainer" class="form-group" style="display:none; margin-top:10px;">
                            <label>Please specify event category <span class="required">*</span></label>
                            <input type="text" name="event_category_other" id="eventCategoryOther" class="form-control" placeholder="Enter custom event category">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Event Type <span class="required">*</span></label>
                        <select name="event_type" id="eventType" class="form-control" required disabled onchange="handleEventTypeChange()">
                            <option value="">Select event type</option>
                        </select>
                        <div id="otherEventContainer" class="form-group" style="display:none; margin-top:10px;">
                            <label>Please specify event type <span class="required">*</span></label>
                            <input type="text" name="event_type_other" id="eventTypeOther" class="form-control" placeholder="Enter custom event type">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Event Date <span class="required">*</span></label>
                            <input type="date" name="event_date" class="form-control" min="<?= date('Y-m-d', strtotime('+2 days')) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Event Time <span style="color:var(--gold);font-size:0.8rem;">(24-hour)</span></label>
                            <div id="eventTimeContainer" class="time-picker-wrapper" style="display:flex;align-items:center;gap:8px;"></div>
                        </div>
                    </div>


                    <div class="step-nav">
                        <div></div>
                        <button type="button" class="btn-next" onclick="showDishTypeModal()">Next: Menu <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                <!-- ════ STEP 2: Menu Customisation ════ -->
                <div class="form-step" id="step2">
                    <h3 style="color:var(--gold);margin-bottom:8px;">Menu Customisation</h3>
                    <p style="color:var(--mid-grey);font-size:0.9rem;margin-bottom:32px;" id="menuSubtitle">Select your protein and build your perfect menu.</p>

                    <!-- Single Dish Mode -->
                    <div id="singleDishSection">
                        <!-- Protein Selection -->
                        <div class="form-group">
                            <label>Select Protein <span class="required">*</span></label>
                            <div class="option-grid" id="proteinGrid">
                                <?php
                                $proteinTypes = ['Chicken', 'Lamb', 'Beef', 'BBQ', 'Vegetarian', 'Vegan'];
                                $proteinIcons = [
                                    'Chicken' => 'fa-drumstick-bite',
                                    'Lamb'    => 'fa-sheep',    
                                    'Beef'    => 'fa-cow',      
                                    'BBQ'     => 'fa-fire',
                                    'Vegetarian' => 'fa-leaf',
                                    'Vegan'   => 'fa-seedling',
                                ];
                                foreach ($proteinTypes as $ptype):
                                ?>
                                <div class="option-card" onclick="selectProtein(this, '<?= $ptype ?>')">
                                    <input type="radio" name="protein_type" value="<?= $ptype ?>">
                                    <span class="checkmark"></span>
                                    <div class="option-info">
                                        <h4><i class="fas <?= $proteinIcons[$ptype] ?>" style="color:var(--gold);margin-right:8px;"></i><?= $ptype ?></h4>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Dynamic Protein Dishes (shown after selection) -->
                        <div id="proteinDishes" style="display:none;">
                            <div class="form-group">
                                <label>Select Dish</label>
                                <div class="option-grid" id="dishOptions"></div>
                            </div>
                            <div class="form-group" id="spiceLevelGroup" style="display:none;">
                                <label>Spice Level</label>
                                <div class="spice-selector" id="spiceLevel">
                                    <button type="button" class="spice-btn" data-value="mild" onclick="selectSpice(this)">🌶️ Mild</button>
                                    <button type="button" class="spice-btn active" data-value="medium" onclick="selectSpice(this)">🌶️🌶️ Medium</button>
                                    <button type="button" class="spice-btn" data-value="hot" onclick="selectSpice(this)">🌶️🌶️🌶️ Hot</button>
                                </div>
                                <input type="hidden" name="spice_level" value="medium">
                            </div>
                        </div>
                    </div>

                    <!-- Multiple Dishes Mode -->
                    <div id="multipleDishesSection" style="display:none;">
                        <div class="form-group">
                            <label>Select Proteins <span style="color:var(--gold);font-size:0.8rem;">(Select multiple options)</span></label>
                            <div class="option-grid" id="multiProteinGrid">
                                <?php
                                foreach ($proteinTypes as $ptype):
                                ?>
                                <div class="option-card" onclick="toggleMultiProtein(this, '<?= $ptype ?>')">
                                    <input type="checkbox" name="protein_types[]" value="<?= $ptype ?>">
                                    <span class="checkmark"></span>
                                    <div class="option-info">
                                        <h4><i class="fas <?= $proteinIcons[$ptype] ?>" style="color:var(--gold);margin-right:8px;"></i><?= $ptype ?></h4>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Multiple Protein Dishes Container -->
                        <div id="multiProteinDishes"></div>

                        <!-- Spice Level for Multiple Dishes -->
                        <div class="form-group" id="multiSpiceLevelGroup" style="display:none;">
                            <label>Spice Level</label>
                            <div class="spice-selector" id="multiSpiceLevel">
                                <button type="button" class="spice-btn" data-value="mild" onclick="selectMultiSpice(this)">🌶️ Mild</button>
                                <button type="button" class="spice-btn active" data-value="medium" onclick="selectMultiSpice(this)">🌶️🌶️ Medium</button>
                                <button type="button" class="spice-btn" data-value="hot" onclick="selectMultiSpice(this)">🌶️🌶️🌶️ Hot</button>
                            </div>
                            <input type="hidden" name="spice_level_multi" value="medium">
                        </div>
                    </div>

                    <!-- Rice -->
                    <div class="form-group">
                        <label>Rice Selection</label>
                        <div class="option-grid" id="riceGrid">
                            <?php 
                            $riceItems = ['Veg Rice', 'Chicken Rice'];
                            $riceDescriptions = [
                                'Veg Rice' => 'Flavorful basmati rice with vegetables',
                                'Chicken Rice' => 'Aromatic chicken-infused basmati rice'
                            ];
                            foreach ($riceItems as $rice): ?>
                            <div class="option-card" onclick="toggleOption(this)">
                                <input type="radio" name="rice_items[]" value="<?= $rice ?>">
                                <span class="checkmark"></span>
                                <div class="option-info">
                                    <h4><?= htmlspecialchars($rice) ?></h4>
                                    <p><?= htmlspecialchars($riceDescriptions[$rice] ?? '') ?></p>
                                </div>
                                <span class="option-price">Included</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Bread -->
                    <div class="form-group">
                        <label>Bread Selection</label>
                        <div class="option-grid" id="breadGrid">
                            <?php 
                            $breadItems = ['Naan', 'Roti', 'Kulcha'];
                            $breadDescriptions = [
                                'Naan' => 'Soft and fluffy Indian bread',
                                'Roti' => 'Whole wheat flatbread',
                                'Kulcha' => 'Leavened Indian bread'
                            ];
                            foreach ($breadItems as $bread): ?>
                            <div class="option-card" onclick="toggleOption(this)">
                                <input type="radio" name="bread_items[]" value="<?= $bread ?>">
                                <span class="checkmark"></span>
                                <div class="option-info">
                                    <h4><?= htmlspecialchars($bread) ?></h4>
                                    <p><?= htmlspecialchars($breadDescriptions[$bread] ?? '') ?></p>
                                </div>
                                <span class="option-price">Included</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Salad -->
                    <div class="form-group">
                        <label>Salad</label>
                        <div class="option-grid" id="saladGrid">
                            <div class="option-card" onclick="toggleOption(this)">
                                <input type="radio" name="salad_items[]" value="Green Salad">
                                <span class="checkmark"></span>
                                <div class="option-info">
                                    <h4>Green Salad</h4>
                                    <p>Fresh mixed greens with house dressing</p>
                                </div>
                                <span class="option-price">Included</span>
                            </div>
                            <div class="option-card" onclick="toggleOption(this)">
                                <input type="radio" name="salad_items[]" value="Raita">
                                <span class="checkmark"></span>
                                <div class="option-info">
                                    <h4>Raita</h4>
                                    <p>Yogurt-based cooling side dish</p>
                                </div>
                                <span class="option-price">Included</span>
                            </div>
                            <div class="option-card" onclick="toggleOption(this)">
                                <input type="radio" name="salad_items[]" value="Pickle/Chutney">
                                <span class="checkmark"></span>
                                <div class="option-info">
                                    <h4>Pickle/Chutney</h4>
                                    <p>Assorted pickles and chutneys</p>
                                </div>
                                <span class="option-price">Included</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sauce -->
                    <div class="form-group">
                        <label>Sauce</label>
                        <div class="option-grid" id="sauceGrid">
                            <div class="option-card" onclick="toggleOption(this)">
                                <input type="radio" name="sauce_items[]" value="Mint Sauce">
                                <span class="checkmark"></span>
                                <div class="option-info">
                                    <h4>Mint Sauce</h4>
                                    <p>Refreshing mint yogurt sauce</p>
                                </div>
                                <span class="option-price">Included</span>
                            </div>
                            <div class="option-card" onclick="toggleOption(this)">
                                <input type="radio" name="sauce_items[]" value="Tamarind Chutney">
                                <span class="checkmark"></span>
                                <div class="option-info">
                                    <h4>Tamarind Chutney</h4>
                                    <p>Sweet and tangy tamarind sauce</p>
                                </div>
                                <span class="option-price">Included</span>
                            </div>
                            <div class="option-card" onclick="toggleOption(this)">
                                <input type="radio" name="sauce_items[]" value="Green Chutney">
                                <span class="checkmark"></span>
                                <div class="option-info">
                                    <h4>Green Chutney</h4>
                                    <p>Fresh coriander and mint chutney</p>
                                </div>
                                <span class="option-price">Included</span>
                            </div>
                        </div>
                    </div>

                    <!-- Desserts -->
                    <div class="form-group">
                        <label>Desserts</label>
                        <div class="option-grid" id="dessertsGrid">
                            <?php if (!empty($menuByCategory['desserts'])): foreach ($menuByCategory['desserts'] as $item): ?>
                            <div class="option-card" onclick="toggleOption(this)">
                                <input type="radio" name="menu_items[]" value="<?= $item['id'] ?>">
                                <span class="checkmark"></span>
                                <div class="option-info">
                                    <h4><?= htmlspecialchars($item['name']) ?></h4>
                                </div>
                                <span class="option-price">Included</span>
                            </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>

                    <!-- Starters (Add-ons) -->
                    <div class="form-group">
                        <label>Starters <span style="color:var(--gold);font-size:0.8rem;">(Add-ons — extra charge per head)</span></label>
                        <div class="option-grid" id="startersGrid">
                            <?php if (!empty($menuByCategory['starters'])): foreach ($menuByCategory['starters'] as $item): ?>
                            <div class="option-card" onclick="toggleOption(this)" data-price="<?= $item['price'] ?>">
                                <input type="checkbox" name="starters[]" value="<?= $item['id'] ?>" data-price="<?= $item['price'] ?>" data-name="<?= htmlspecialchars($item['name']) ?>">
                                <span class="checkmark"></span>
                                <div class="option-info">
                                    <h4><?= htmlspecialchars($item['name']) ?></h4>
                                    <p><?= htmlspecialchars($item['description']) ?></p>
                                </div>
                                <span class="option-price"><?= formatCurrency($item['price']) ?>/head</span>
                            </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>

                    <!-- Drinks -->
                    <div class="form-group">
                        <label>Drinks</label>
                        <div class="option-grid" id="drinksGrid">
                            <?php if (!empty($menuByCategory['drinks'])): foreach ($menuByCategory['drinks'] as $item): ?>
                            <div class="option-card" onclick="toggleOption(this)" data-price="<?= $item['price'] ?>">
                                <input type="checkbox" name="drinks[]" value="<?= $item['id'] ?>" data-price="<?= $item['price'] ?>" data-name="<?= htmlspecialchars($item['name']) ?>">
                                <span class="checkmark"></span>
                                <div class="option-info">
                                    <h4><?= htmlspecialchars($item['name']) ?></h4>
                                </div>
                                <?php if ($item['price'] > 0): ?>
                                <span class="option-price"><?= formatCurrency($item['price']) ?>/head</span>
                                <?php else: ?>
                                <span class="option-price" style="color:var(--success);">Free</span>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; endif; ?>
                            <?php if ($isSummer): ?>
                            <div class="option-card selected" style="border-color:var(--success);pointer-events:none;">
                                <span class="checkmark" style="border-color:var(--success);background:var(--success);">
                                    <span style="color:#fff;font-size:0.7rem;">✓</span>
                                </span>
                                <div class="option-info">
                                    <h4>🍹 Mojito Mocktail</h4>
                                    <p>Summer special — FREE!</p>
                                </div>
                                <span class="option-price" style="color:var(--success);">FREE</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="step-nav">
                        <button type="button" class="btn-prev" onclick="prevStep(2)"><i class="fas fa-arrow-left"></i> Back</button>
                        <button type="button" class="btn-next" onclick="nextStep(2)">Next: Guests <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                <!-- ════ STEP 3: Guest Count & Allergies ════ -->
                <div class="form-step" id="step3">
                    <h3 style="color:var(--gold);margin-bottom:8px;">Guest Count & Dietary Requirements</h3>
                    <p style="color:var(--mid-grey);font-size:0.9rem;margin-bottom:32px;">Tell us about your guests so we can plan perfectly.</p>

                    <!-- Guest Inputs Grid -->
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:24px;margin-bottom:24px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label><i class="fas fa-users" style="color:var(--gold);margin-right:8px;"></i>Total Guests <span class="required">*</span></label>
                            <input type="number" name="guest_count" class="form-control" min="1" max="1000" placeholder="Number of guests..." required oninput="updateCalculator()" style="font-weight:600;font-size:1.1rem;padding:16px;">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label><i class="fas fa-child" style="color:var(--gold);margin-right:8px;"></i>Children (Under 4)</label>
                            <input type="number" name="kids_count" class="form-control" min="0" max="100" value="0" oninput="updateCalculator()" style="font-weight:600;font-size:1.1rem;padding:16px;">
                        </div>
                    </div>

                    <div class="info-note" style="background:rgba(212,175,55,0.05);border:1px solid rgba(212,175,55,0.15);border-radius:var(--radius-md);padding:14px 20px;margin-bottom:28px;">
                        <div style="display:flex;align-items:center;gap:14px;">
                            <div style="width:36px;height:36px;background:var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--black);flex-shrink:0;">
                                <i class="fas fa-gift"></i>
                            </div>
                            <span style="color:var(--charcoal);font-size:0.92rem;font-weight:500;">Children under 4 years old are <strong style="color:var(--gold-dark);font-weight:700;">COMPLIMENTARY</strong></span>
                        </div>
                    </div>

                    <!-- Pricing Info Alert -->
                    <div class="alert alert-info" style="margin-bottom:32px;border-radius:var(--radius-md);border-left-width:4px;">
                        <div style="display:flex;gap:12px;">
                            <i class="fas fa-info-circle text-info" style="font-size:1.2rem;margin-top:2px;"></i>
                            <p style="margin:0;font-size:0.88rem;line-height:1.5;"><strong>Exclusive Tier Pricing:</strong> 50+ guests receive a 5% discount. 100+ guests receive free London delivery. High-capacity events (150+) include complimentary waiter service.</p>
                        </div>
                    </div>

                    <!-- Dietary Section -->
                    <div class="form-group" style="margin-top:40px;">
                        <label style="font-size:0.95rem;font-weight:600;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;">
                            <span>Special Dietary Requirements</span>
                            <span style="font-size:0.75rem;color:var(--gold-dark);font-weight:600;background:rgba(212,175,55,0.1);padding:4px 10px;border-radius:var(--radius-pill);">£<?= number_format(ALLERGY_SURCHARGE, 2) ?> / per head</span>
                        </label>
                        
                        <div id="allergySection" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:14px;">
                            <?php
                            $allergyTypes = ['Dairy Free', 'Gluten Free', 'Nut Free', 'Vegan'];
                            foreach ($allergyTypes as $allergy):
                            ?>
                            <div class="allergy-row" style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:var(--light-grey);border-radius:var(--radius-md);border:1px solid var(--border-color);transition:all var(--transition);">
                                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;margin-bottom:0;flex:1;font-weight:500;font-size:0.9rem;">
                                    <input type="checkbox" name="allergy_types[]" value="<?= htmlspecialchars($allergy) ?>" onchange="toggleAllergyCount(this)" style="accent-color:var(--gold);width:18px;height:18px;">
                                    <?= htmlspecialchars($allergy) ?>
                                </label>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span style="font-size:0.75rem;color:var(--mid-grey);text-transform:uppercase;letter-spacing:0.5px;">Guests:</span>
                                    <input type="number" name="allergy_counts[<?= htmlspecialchars($allergy) ?>]" class="form-control" min="0" value="0" style="width:65px;padding:6px 10px;font-weight:600;text-align:center;" disabled oninput="updateCalculator()">
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="step-nav">
                        <button type="button" class="btn-prev" onclick="prevStep(3)"><i class="fas fa-arrow-left"></i> Back</button>
                        <button type="button" class="btn-next" onclick="nextStep(3)">Next: Services <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                <!-- ════ STEP 4: Additional Services ════ -->
                <div class="form-step" id="step4">
                    <h3 style="color:var(--gold);margin-bottom:8px;">Additional Services</h3>
                    <p style="color:var(--mid-grey);font-size:0.9rem;margin-bottom:32px;">Enhance your event with our premium add-on services.</p>

                    <div class="option-grid" style="grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));" id="servicesGrid">
                        <?php foreach ($services as $key => $svc): ?>
                        <?php if ($key === 'screens'): ?>
                        <div class="option-card service-unavailable" data-unavailable="true" onmouseover="showServiceMessage(this, 'This service will start soon')" onclick="handleUnavailableService(this, 'This service will start soon')">
                            <input type="checkbox" name="services[]" value="<?= htmlspecialchars($key) ?>" data-price="<?= $svc['price'] ?>" data-name="<?= htmlspecialchars($svc['name']) ?>" disabled>
                            <span class="checkmark" style="opacity:0.5;"></span>
                            <div class="option-info">
                                <h4><?= htmlspecialchars($svc['name']) ?></h4>
                                <p style="font-size:0.8rem;color:var(--mid-grey);">Coming soon</p>
                            </div>
                            <span class="option-price" style="opacity:0.5;"><?= formatCurrency($svc['price']) ?></span>
                        </div>
                        <?php else: ?>
                        <div class="option-card" onclick="toggleOption(this)" data-price="<?= $svc['price'] ?>">
                            <input type="checkbox" name="services[]" value="<?= htmlspecialchars($key) ?>" data-price="<?= $svc['price'] ?>" data-name="<?= htmlspecialchars($svc['name']) ?>">
                            <span class="checkmark"></span>
                            <div class="option-info">
                                <h4><?= htmlspecialchars($svc['name']) ?></h4>
                            </div>
                            <span class="option-price"><?= formatCurrency($svc['price']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <!-- Disposable Cutlery -->
                        <div class="option-card" onclick="toggleOption(this)" data-price="0">
                            <input type="checkbox" name="services[]" value="disposable_cutlery" data-price="0" data-name="Disposable Cutlery">
                            <span class="checkmark"></span>
                            <div class="option-info">
                                <h4><i class="fas fa-utensils" style="color:var(--gold);margin-right:8px;"></i>Disposable Cutlery</h4>
                                <p style="font-size:0.8rem;color:var(--mid-grey);">Plates, forks, spoons, cups</p>
                            </div>
                            <span class="option-price" style="color:var(--success);">FREE</span>
                        </div>
                        
                        </div>
                    <!-- Service Message Toast -->
                    <div id="serviceMessage" style="display:none;position:fixed;bottom:30px;left:50%;transform:translateX(-50%);background:var(--charcoal);border:1px solid var(--gold);border-radius:8px;padding:14px 24px;z-index:1000;box-shadow:0 8px 32px rgba(0,0,0,0.3);">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <i class="fas fa-info-circle" style="color:var(--gold);"></i>
                            <span style="color:var(--white);" id="serviceMessageText"></span>
                        </div>
                    </div>

                    <div class="step-nav">
                        <button type="button" class="btn-prev" onclick="prevStep(4)"><i class="fas fa-arrow-left"></i> Back</button>
                        <button type="button" class="btn-next" onclick="nextStep(4)">Next: Location <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                <!-- ════ STEP 5: Location ════ -->
                <div class="form-step" id="step5">
                    <h3 style="color:var(--gold);margin-bottom:8px;">Event Location</h3>
                    <p style="color:var(--mid-grey);font-size:0.9rem;margin-bottom:32px;">Where should we deliver the magic?</p>

                    <!-- Modern Location Search -->
                    <div class="location-search-container">
                        <div class="location-search-wrapper">
                            <div class="search-input-group">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" name="postcode" id="autocompletePostcode" class="location-search-input" placeholder="Enter postcode or location (e.g. SW1A 1AA, London, Manchester...)" required oninput="handlePostcodeInput(this.value)" onblur="hideAddressDropdownDelayed()">
                                <button type="button" class="location-search-btn" onclick="lookupPostcode()">
                                    <i class="fas fa-crosshairs"></i>
                                </button>
                            </div>
                            <div id="postcodeResults" class="location-suggestions" style="display:none;"></div>
                        </div>

                        <!-- Quick Location Buttons -->
                        <div class="quick-locations">
                            <span class="quick-label">Quick search:</span>
                            <button type="button" class="quick-location-btn" onclick="quickLocationSearch('London')">London</button>
                            <button type="button" class="quick-location-btn" onclick="quickLocationSearch('Manchester')">Manchester</button>
                            <button type="button" class="quick-location-btn" onclick="quickLocationSearch('Birmingham')">Birmingham</button>
                            <button type="button" class="quick-location-btn" onclick="quickLocationSearch('Leeds')">Leeds</button>
                        </div>
                    </div>

                    <!-- Modern Map Container -->
                    <div class="map-section">
                        <div class="map-header">
                            <i class="fas fa-map-marked-alt" style="color:var(--gold);margin-right:8px;"></i>
                            <span class="map-title">Select Your Location</span>
                            <span class="map-subtitle" id="mapSubtitle">Enter a location above to view the map</span>
                        </div>

                        <div id="mapContainer" class="modern-map-container">
                            <div id="map" class="map-canvas">
                                <!-- Map placeholder with modern styling -->
                                <div class="map-placeholder">
                                    <div class="map-placeholder-content">
                                        <i class="fas fa-map-marker-alt location-pin"></i>
                                        <h4>Interactive Map</h4>
                                        <p>Enter your postcode above to view the interactive map and select your exact location</p>
                                        <div class="map-features">
                                            <span class="feature-tag"><i class="fas fa-search"></i> Smart Search</span>
                                            <span class="feature-tag"><i class="fas fa-route"></i> Route Planning</span>
                                            <span class="feature-tag"><i class="fas fa-clock"></i> Delivery Zones</span>
                                        </div>
                                    </div>
                                    <!-- Simulated map grid -->
                                    <div class="map-grid-overlay">
                                        <div class="map-grid-line horizontal"></div>
                                        <div class="map-grid-line horizontal"></div>
                                        <div class="map-grid-line horizontal"></div>
                                        <div class="map-grid-line vertical"></div>
                                        <div class="map-grid-line vertical"></div>
                                        <div class="map-grid-line vertical"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Map Controls -->
                            <div class="map-controls">
                                <button class="map-control-btn" title="Zoom In">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button class="map-control-btn" title="Zoom Out">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button class="map-control-btn" title="My Location">
                                    <i class="fas fa-crosshairs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Location Details -->
                    <div class="location-details" id="locationDetails" style="display:none;">
                        <div class="selected-location-card">
                            <div class="location-header">
                                <i class="fas fa-check-circle" style="color:var(--success);"></i>
                                <span class="location-title">Selected Location</span>
                            </div>
                            <div class="location-info">
                                <div class="location-address" id="selectedAddress">—</div>
                                <div class="location-meta">
                                    <span id="locationPostcode">—</span>
                                    <span class="delivery-badge">
                                        <i class="fas fa-truck"></i> Free delivery available
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Additional Instructions</label>
                        <textarea name="instructions" class="form-control" placeholder="Parking info, access instructions, special setup requirements, security codes, etc."></textarea>
                    </div>

                    <!-- Hidden inputs for coordinates -->
                    <input type="hidden" name="latitude" id="latitudeInput">
                    <input type="hidden" name="longitude" id="longitudeInput">
                    <input type="hidden" name="full_address" id="fullAddressInput">

                    <div class="step-nav">
                        <button type="button" class="btn-prev" onclick="prevStep(5)"><i class="fas fa-arrow-left"></i> Back</button>
                        <button type="button" class="btn-next" onclick="nextStep(5)">Next: Summary <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
                            <div id="postcodeResults" class="address-suggestions" style="display:none;margin-top:4px;"></div>
                        </div>
                        <div class="form-group">
                            <label>House Number <span class="required">*</span></label>
                            <select name="house_number" id="houseNumberSelect" class="form-control" required disabled>
                                <option value="">Select postcode first</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Venue Address <span class="required">*</span></label>
                        <input type="text" name="address" id="autocompleteAddress" class="form-control" placeholder="Full address will be auto-filled..." required>
                        <input type="hidden" name="full_address" id="fullAddressInput">
                    </div>



                    <!-- Map -->
                    <div class="form-group">
                        <div id="mapContainer" style="width:100%;height:350px;border-radius:var(--radius);overflow:hidden;border:1px solid rgba(212,175,55,0.3);">
                            <div id="map" style="width:100%;height:100%;background:var(--charcoal);display:flex;align-items:center;justify-content:center;">
                                <span style="color:var(--mid-grey);font-size:0.95rem;"><i class="fas fa-map-marker-alt" style="color:var(--gold);margin-right:8px;"></i>Enter postcode to view map</span>
                            </div>
                        </div>
                        <input type="hidden" name="latitude" id="latitudeInput">
                        <input type="hidden" name="longitude" id="longitudeInput">
                    </div>

                    <div class="form-group">
                        <label>Additional Instructions</label>
                        <textarea name="instructions" class="form-control" placeholder="Parking info, access instructions, special setup requirements…"></textarea>
                    </div>

                    <div class="step-nav">
                        <button type="button" class="btn-prev" onclick="prevStep(5)"><i class="fas fa-arrow-left"></i> Back</button>
                        <button type="button" class="btn-next" onclick="nextStep(5)">Next: Summary <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                <!-- ════ STEP 6: Price Summary & Checkout ════ -->
                <div class="form-step" id="step6">
                    <div style="text-align:center;margin-bottom:40px;">
                        <h3 style="color:var(--gold);font-size:1.8rem;margin-bottom:8px;font-family:var(--font-heading);">Booking Summary & Payment</h3>
                        <p style="color:var(--mid-grey);font-size:0.95rem;">Review your bespoke catering experience and complete your deposit.</p>
                    </div>

                    <!-- Summary Grid -->
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:24px;margin-bottom:40px;">
                        <!-- Event & Location -->
                        <div style="background:var(--light-grey);padding:24px;border-radius:var(--radius-lg);border:1px solid var(--border-color);position:relative;overflow:hidden;box-shadow:var(--shadow-soft);">
                            <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:var(--gold);"></div>
                            <h4 style="color:var(--gold-dark);font-size:0.75rem;text-transform:uppercase;letter-spacing:2px;margin-bottom:16px;font-weight:700;display:flex;align-items:center;gap:8px;">
                                <i class="fas fa-calendar-check" style="font-size:0.9rem;"></i> Event Details
                            </h4>
                            <div style="display:flex;flex-direction:column;gap:12px;">
                                <div style="display:flex;align-items:center;gap:12px;font-size:0.92rem;">
                                    <i class="fas fa-calendar-day" style="color:var(--gold-dark);width:16px;"></i>
                                    <span id="summaryDate" style="color:var(--charcoal);font-weight:500;">—</span>
                                </div>
                                <div style="display:flex;align-items:center;gap:12px;font-size:0.92rem;">
                                    <i class="fas fa-map-marker-alt" style="color:var(--gold-dark);width:16px;"></i>
                                    <span id="summaryVenue" style="color:var(--charcoal);line-height:1.4;">—</span>
                                </div>
                                <div style="display:flex;align-items:center;gap:12px;font-size:0.92rem;">
                                    <i class="fas fa-utensils" style="color:var(--gold-dark);width:16px;"></i>
                                    <span id="summaryEvent" style="color:var(--charcoal);">—</span>
                                </div>
                            </div>
                        </div>

                        <!-- Guest Breakdown -->
                        <div style="background:var(--light-grey);padding:24px;border-radius:var(--radius-lg);border:1px solid var(--border-color);position:relative;overflow:hidden;box-shadow:var(--shadow-soft);">
                            <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:var(--gold);"></div>
                            <h4 style="color:var(--gold-dark);font-size:0.75rem;text-transform:uppercase;letter-spacing:2px;margin-bottom:16px;font-weight:700;display:flex;align-items:center;gap:8px;">
                                <i class="fas fa-users" style="font-size:0.9rem;"></i> Attendance
                            </h4>
                            <div id="summaryGuestBreakdown" style="display:flex;flex-direction:column;gap:10px;">
                                <div style="display:flex;justify-content:space-between;font-size:0.95rem;padding-bottom:8px;border-bottom:1px solid rgba(0,0,0,0.05);">
                                    <span style="color:var(--mid-grey);">Total Guests:</span>
                                    <span id="summaryTotalGuests" style="font-weight:700;color:var(--charcoal);">0</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;font-size:0.9rem;">
                                    <span style="color:var(--mid-grey);">Adults:</span>
                                    <span id="summaryAdults" style="font-weight:600;color:var(--charcoal);">0</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;font-size:0.9rem;">
                                    <span style="color:var(--mid-grey);">Children (Free):</span>
                                    <span id="summaryChildren" style="font-weight:600;color:var(--success);">0</span>
                                </div>
                                <div id="summaryAllergyBreakdown" style="margin-top:4px;">
                                    <!-- Allergy breakdown injected by JS -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Menu Items Display -->
                    <div style="background:var(--white);padding:32px;border-radius:var(--radius-lg);border:1px solid rgba(212,175,55,0.2);margin-bottom:40px;box-shadow:var(--shadow-soft);">
                        <h4 style="color:var(--gold-dark);font-size:0.8rem;text-transform:uppercase;letter-spacing:2px;margin-bottom:20px;font-weight:700;display:flex;align-items:center;gap:12px;">
                            <i class="fas fa-list-ul" style="font-size:1rem;"></i> Your Bespoke Menu
                        </h4>
                        <div id="summaryMenu" style="font-size:0.95rem;color:var(--charcoal);line-height:1.8;columns:2;column-gap:40px;">
                            <p style="color:var(--mid-grey);font-style:italic;">Loading your selection...</p>
                        </div>
                    </div>

                    <!-- Price Breakdown Panel -->
                    <div class="price-summary" id="priceSummary" style="background:#fafafa;border:1px solid var(--border-color);border-radius:var(--radius-lg);padding:36px;box-shadow:inset 0 2px 10px rgba(0,0,0,0.02);">
                        <h3 style="margin-bottom:28px;color:var(--charcoal);border-bottom:2px solid rgba(212,175,55,0.15);padding-bottom:16px;display:flex;align-items:center;gap:12px;font-family:var(--font-heading);">
                            <i class="fas fa-file-invoice-dollar" style="color:var(--gold);"></i> Investment Breakdown
                        </h3>
                        
                        <div style="display:flex;flex-direction:column;gap:16px;">
                            <div class="price-line">
                                <span class="label" style="font-weight:500;color:var(--mid-grey);">Catering & Service (<span id="priceBillable" style="color:var(--gold-dark);font-weight:700;">0</span> Billable Guests)</span>
                                <span class="value" id="priceSubtotal" style="font-weight:600;color:var(--charcoal);">£0.00</span>
                            </div>
                            <div class="price-line discount" id="priceDiscountRow" style="display:none;background:rgba(46,204,113,0.08);padding:10px 16px;border-radius:var(--radius);margin:4px -16px;">
                                <span class="label" style="color:var(--success);font-weight:600;"><i class="fas fa-star"></i> Loyalty Discount (50+ Guests)</span>
                                <span class="value" id="priceDiscount" style="color:var(--success);font-weight:700;">-£0.00</span>
                            </div>
                            <!-- Small lines for other costs -->
                            <div id="smallPriceLines" style="display:flex;flex-direction:column;gap:12px;padding:4px 0;">
                                <div class="price-line" id="priceAllergyRow" style="display:none;">
                                    <span class="label" style="color:var(--mid-grey);">Dietary Surcharges</span>
                                    <span class="value" id="priceAllergy" style="color:var(--charcoal);">£0.00</span>
                                </div>
                                <div class="price-line" id="priceStartersRow" style="display:none;">
                                    <span class="label" style="color:var(--mid-grey);">Menu Add-ons</span>
                                    <span class="value" id="priceStarters" style="color:var(--charcoal);">£0.00</span>
                                </div>
                                <div class="price-line" id="priceDrinksRow" style="display:none;">
                                    <span class="label" style="color:var(--mid-grey);">Beverages</span>
                                    <span class="value" id="priceDrinks" style="color:var(--charcoal);">£0.00</span>
                                </div>
                                <div class="price-line" id="priceServicesRow" style="display:none;">
                                    <span class="label" style="color:var(--mid-grey);">Premium Services</span>
                                    <span class="value" id="priceServices" style="color:var(--charcoal);">£0.00</span>
                                </div>
                                <div class="price-line" id="priceDeliveryRow">
                                    <span class="label" style="color:var(--mid-grey);">Delivery (London Area)</span>
                                    <span class="value" id="priceDelivery" style="color:var(--charcoal);">£<?= number_format(DELIVERY_CHARGE, 2) ?></span>
                                </div>
                            </div>
                            
                            <div class="price-line" style="margin-top:8px;padding-top:16px;border-top:1px solid rgba(0,0,0,0.05);">
                                <span class="label" style="color:var(--mid-grey);">VAT (20%)</span>
                                <span class="value" id="priceVat" style="color:var(--charcoal);font-weight:500;">£0.00</span>
                            </div>
                            <div class="price-line total" style="margin-top:10px;padding:24px;background:var(--white);border:2px solid var(--gold);border-radius:var(--radius-lg);box-shadow:var(--shadow-glow);">
                                <span class="label" style="font-size:1.15rem;font-weight:700;color:var(--charcoal);">Grand Total Investment</span>
                                <span class="value" id="priceTotal" style="font-size:1.8rem;color:var(--gold-dark);font-weight:800;">£0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Tier Card Selection -->
                    <div style="margin-top:56px;">
                        <h4 style="text-align:center;font-weight:700;margin-bottom:24px;color:var(--charcoal);letter-spacing:0.5px;">Choose Your Deposit Plan</h4>
                        <div class="option-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));gap:16px;">
                            <?php 
                            $tiers = [
                                10 => ['label' => 'Standard Deposit', 'desc' => 'Secure your date'],
                                25 => ['label' => 'Priority Booking', 'desc' => 'Quarter payment'],
                                50 => ['label' => 'Partial Payment', 'desc' => 'Half deposit'],
                                100 => ['label' => 'Full Payment', 'desc' => 'Hassle-free']
                            ];
                            foreach ($tiers as $pct => $info): ?>
                            <div class="option-card <?= $pct === 10 ? 'selected' : '' ?>" onclick="selectPaymentTier(this, <?= $pct ?>)" style="padding:24px 16px;text-align:center;align-items:center;">
                                <input type="radio" name="payment_percent" value="<?= $pct ?>" <?= $pct === 10 ? 'checked' : '' ?>>
                                <div style="font-size:2.2rem;font-weight:800;color:var(--gold);margin-bottom:8px;line-height:1;"><?= $pct ?><span style="font-size:1rem;vertical-align:top;margin-left:2px;">%</span></div>
                                <div style="font-size:0.8rem;font-weight:700;color:var(--charcoal);text-transform:uppercase;letter-spacing:1px;"><?= $info['label'] ?></div>
                                <p style="font-size:0.75rem;color:var(--mid-grey);margin-top:4px;line-height:1.3;"><?= $info['desc'] ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Modern Payment Summary -->
                    <div id="paymentTierSummary" style="margin-top:32px;padding:32px;background:linear-gradient(135deg, #1a1a1a, #000);border-radius:var(--radius-lg);color:var(--white);display:flex;justify-content:space-between;align-items:center;box-shadow:var(--shadow-heavy);">
                        <div style="display:flex;align-items:center;gap:20px;">
                            <div style="width:56px;height:56px;background:rgba(212,175,55,0.1);border:1px solid var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--gold);font-size:1.4rem;">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div>
                                <span style="font-size:0.8rem;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:2px;display:block;margin-bottom:4px;">Deposit Payable Today</span>
                                <div style="font-size:2.4rem;font-weight:800;color:var(--gold);line-height:1;" id="amountPayableNow">£0.00</div>
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <div style="color:var(--success);font-size:1.1rem;font-weight:600;margin-bottom:4px;display:flex;align-items:center;justify-content:flex-end;gap:8px;">
                                <i class="fas fa-shield-check"></i> SSL Secured
                            </div>
                            <span style="font-size:0.75rem;color:rgba(255,255,255,0.4);">Encrypted payment via Stripe</span>
                        </div>
                    </div>

                    <!-- Premium Stripe Container -->
                    <div id="stripePaymentSection" style="margin-top:48px;">
                        <label style="font-weight:700;color:var(--charcoal);margin-bottom:16px;display:flex;align-items:center;gap:12px;font-size:1rem;">
                            <i class="fab fa-cc-visa" style="color:#1a1f71;font-size:1.5rem;"></i>
                            <i class="fab fa-cc-mastercard" style="color:#eb001b;font-size:1.5rem;"></i>
                            <span>Pay Securely with Card</span>
                        </label>
                        <div id="card-element-container" style="background:var(--white);padding:24px;border-radius:var(--radius-lg);border:1px solid var(--border-color);box-shadow:var(--shadow-soft);transition:all var(--transition);">
                            <div id="card-element"><!-- Stripe Element --></div>
                            <div id="card-errors" role="alert" style="color:var(--danger);font-size:0.85rem;margin-top:12px;display:none;font-weight:500;"></div>
                        </div>
                        <p style="margin-top:16px;font-size:0.8rem;color:var(--mid-grey);text-align:center;">
                            By clicking "Confirm & Pay", you agree to our <a href="#" style="color:var(--gold-dark);text-decoration:underline;font-weight:500;">Catering Terms of Service</a>.
                        </p>
                    </div>

                    <!-- Step Navigation -->
                    <div class="step-nav" style="margin-top:48px;">
                        <button type="button" class="btn-prev" onclick="prevStep(6)"><i class="fas fa-arrow-left"></i> Back</button>
                        <button type="button" class="btn-submit" id="submitBtn" onclick="submitBooking(event); return false;" style="padding:16px 48px;font-size:1.05rem;">
                            <i class="fas fa-check-circle" style="margin-right:8px;"></i> Confirm & Pay Deposit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- ═══════════════ TESTIMONIALS ═══════════════ -->
    <section class="testimonials" id="testimonials">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-label">Client Stories</span>
                <h2 class="section-title">What Our Clients Say</h2>
                <div class="gold-line"></div>
            </div>

            <!-- Filters -->
            <div class="testimonial-filters">
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="Wedding">Wedding</button>
                <button class="filter-btn" data-filter="Corporate">Corporate</button>
                <button class="filter-btn" data-filter="Private Party">Private Party</button>
            </div>

            <?php if (!empty($testimonials)): ?>
            <div class="testimonial-carousel" id="testimonialCarousel">
                <div class="testimonial-track" id="testimonialTrack">
                    <?php foreach ($testimonials as $t): ?>
                    <div class="testimonial-card" data-category="<?= htmlspecialchars($t['event_type']) ?>">
                        <div class="testimonial-stars">
                            <?php for ($i = 0; $i < $t['rating']; $i++): ?>★<?php endfor; ?>
                            <?php for ($i = $t['rating']; $i < 5; $i++): ?>☆<?php endfor; ?>
                        </div>
                        <p class="testimonial-text">"<?= htmlspecialchars($t['review']) ?>"</p>
                        <div class="testimonial-author-wrapper">
                            <div class="testimonial-avatar"><?= strtoupper(substr($t['name'], 0, 1)) ?></div>
                            <div class="testimonial-author"><?= htmlspecialchars($t['name']) ?></div>
                            <div class="testimonial-event"><?= htmlspecialchars($t['event_type']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="carousel-controls">
                </div>
                <div class="carousel-dots" id="carouselDots"></div>
            </div>
            <?php else: ?>
            <p style="text-align:center;color:var(--mid-grey);">Testimonials coming soon.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ═══════════════ GALLERY ═══════════════ -->
    <section class="gallery-section" id="gallery">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-label">Our Portfolio</span>
                <h2 class="section-title">Event Gallery</h2>
                <div class="gold-line"></div>
            </div>

            <?php if (!empty($galleryItems)): ?>
            <div class="gallery-grid">
                <?php foreach ($galleryItems as $g): ?>
                <div class="gallery-item fade-in" onclick="openLightbox('<?= SITE_URL ?>/assets/images/gallery/<?= htmlspecialchars($g['image_path']) ?>')">
                    <img src="<?= SITE_URL ?>/assets/images/gallery/<?= htmlspecialchars($g['image_path']) ?>" alt="<?= htmlspecialchars($g['caption'] ?? 'Event photo') ?>" loading="lazy">
                    <div class="gallery-overlay">
                        <span><?= htmlspecialchars($g['caption'] ?? $g['category']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <!-- Premium placeholder gallery with professional images -->
            <div class="gallery-grid">
                <?php
                $galleryPlaceholders = [
                    ['url' => 'https://images.unsplash.com/photo-1551218808-94e220e084d2?w=400&h=300&fit=crop&crop=center&auto=format&q=90', 'caption' => 'Wedding Reception Setup'],
                    ['url' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=400&h=300&fit=crop&crop=center&auto=format&q=90', 'caption' => 'Corporate Event Catering'],
                    ['url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&h=300&fit=crop&crop=center&auto=format&q=90', 'caption' => 'Luxury Private Dinner'],
                    ['url' => 'https://images.unsplash.com/photo-1551782450-17144efb5723?w=400&h=300&fit=crop&crop=center&auto=format&q=90', 'caption' => 'Fine Dining Experience'],
                    ['url' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=400&h=300&fit=crop&crop=center&auto=format&q=90', 'caption' => 'Event Hall Setup'],
                    ['url' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?w=400&h=300&fit=crop&crop=center&auto=format&q=90', 'caption' => 'Conference Catering']
                ];
                foreach ($galleryPlaceholders as $placeholder):
                ?>
                <div class="gallery-item fade-in" onclick="openLightbox('<?= $placeholder['url'] ?>')">
                    <img src="<?= $placeholder['url'] ?>" alt="Luxury catering event" loading="lazy">
                    <div class="gallery-overlay">
                        <span><?= $placeholder['caption'] ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox">
        <button class="lightbox-close" onclick="closeLightbox()"><i class="fas fa-times"></i></button>
        <img id="lightboxImg" src="" alt="Gallery image">
    </div>

    <!-- Dish Type Selection Modal -->
    <div class="modal-overlay" id="dishTypeModal">
        <div class="modal-box" style="max-width:480px;background:var(--charcoal);border:1px solid rgba(212,175,55,0.2);">
            <div style="text-align:center;margin-bottom:24px;">
                <div style="width:60px;height:60px;background:rgba(212,175,55,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="fas fa-utensils" style="color:var(--gold);font-size:1.5rem;"></i>
                </div>
                <h2 style="color:var(--white);margin-bottom:8px;font-size:1.5rem;">Choose Your Menu Style</h2>
                <p style="color:var(--mid-grey);font-size:0.95rem;">Select how you'd like to customise your catering experience</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <button type="button" class="dish-type-btn" onclick="selectDishType('single')">
                    <div style="display:flex;align-items:center;gap:16px;">
                        <div style="width:48px;height:48px;background:rgba(212,175,55,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-utensil-spoon" style="color:var(--gold);font-size:1.2rem;"></i>
                        </div>
                        <div style="text-align:left;">
                            <h4 style="color:var(--white);margin-bottom:4px;font-size:1.1rem;">Single Dish</h4>
                            <p style="color:var(--mid-grey);font-size:0.85rem;">One protein with selected dishes</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right" style="color:var(--mid-grey);"></i>
                </button>
                <button type="button" class="dish-type-btn" onclick="selectDishType('multiple')">
                    <div style="display:flex;align-items:center;gap:16px;">
                        <div style="width:48px;height:48px;background:rgba(212,175,55,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-layer-group" style="color:var(--gold);font-size:1.2rem;"></i>
                        </div>
                        <div style="text-align:left;">
                            <h4 style="color:var(--white);margin-bottom:4px;font-size:1.1rem;">Multiple Dishes</h4>
                            <p style="color:var(--mid-grey);font-size:0.85rem;">Multiple proteins with variety of dishes</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right" style="color:var(--mid-grey);"></i>
                </button>
            </div>
        </div>
    </div>

    <style>
    .dish-type-btn {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 12px;
        padding: 16px 20px;
        cursor: pointer;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }
    .dish-type-btn:hover {
        background: rgba(212,175,55,0.08);
        border-color: rgba(212,175,55,0.3);
        transform: translateY(-2px);
    }
    .dish-type-btn:active {
        transform: translateY(0);
    }
    </style>

    <!-- Booking Success Modal -->
    <div class="modal-overlay" id="successModal">
        <div class="modal-box">
            <div class="success-icon"><i class="fas fa-check"></i></div>
            <h2>Booking Confirmed!</h2>
            <p>Thank you for choosing ZAMAHI Luxury Catering.</p>
            <div class="ref-number" id="modalRef">ZAM-XXXXXXXX-XXXXXX</div>
            <p style="font-size:0.85rem;">A confirmation email has been sent to your email address.<br>Our team will contact you within 24 hours.</p>
            <button class="btn btn-gold" style="margin-top:24px;" onclick="closeModal()">Close</button>
        </div>
    </div>

    <!-- Stripe.js -->
    <script src="https://js.stripe.com/v3/"></script>

    <!-- Pass config to JS -->
    <script>
    const ZAMAHI = {
        siteUrl: '<?= SITE_URL ?>',
        basePrice: <?= BASE_PRICE_PER_HEAD ?>,
        vatRate: <?= VAT_RATE ?>,
        allergySurcharge: <?= ALLERGY_SURCHARGE ?>,
        discount50: <?= DISCOUNT_50_GUESTS ?>,
        freeDeliveryThreshold: <?= FREE_DELIVERY_THRESHOLD ?>,
        freeWaiterThreshold: <?= FREE_WAITER_THRESHOLD ?>,
        deliveryCharge: <?= DELIVERY_CHARGE ?>,
        waiterCharge: <?= WAITER_CHARGE ?>,
        csrfToken: '<?= $csrfToken ?>',
        stripePublishableKey: '<?= STRIPE_PUBLISHABLE_KEY ?>'
    };
    </script>
    <script src="<?= SITE_URL ?>/assets/js/booking.js"></script>
    <script src="<?= SITE_URL ?>/assets/js/calculator.js"></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
