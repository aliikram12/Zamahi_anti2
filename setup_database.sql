-- ============================================
-- ZAMAHI LUXURY CATERING - Database Setup
-- ============================================

CREATE DATABASE IF NOT EXISTS zamahi_catering CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE zamahi_catering;

-- ============================================
-- ADMIN USERS
-- ============================================
CREATE TABLE IF NOT EXISTS users_admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- MENU ITEMS
-- ============================================
CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category ENUM('protein','rice','bread','sides','desserts','starters','drinks') NOT NULL,
    sub_category VARCHAR(100) DEFAULT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL,
    price DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    is_addon TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- BOOKINGS (master)
-- ============================================
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ref_number VARCHAR(20) NOT NULL UNIQUE,
    customer_name VARCHAR(150) NOT NULL,
    customer_email VARCHAR(150) NOT NULL,
    customer_phone VARCHAR(30) NOT NULL,
    event_category VARCHAR(50) DEFAULT NULL,
    event_sub_category VARCHAR(100) DEFAULT NULL,
    event_type VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME DEFAULT NULL,
    guest_count INT NOT NULL DEFAULT 1,
    kids_count INT NOT NULL DEFAULT 0,
    kids_free INT NOT NULL DEFAULT 0,
    indoor_outdoor ENUM('indoor','outdoor') DEFAULT 'indoor',
    address TEXT DEFAULT NULL,
    postcode VARCHAR(15) DEFAULT NULL,
    lat DECIMAL(10,7) DEFAULT NULL,
    lng DECIMAL(10,7) DEFAULT NULL,
    venue_photo VARCHAR(255) DEFAULT NULL,
    instructions TEXT DEFAULT NULL,
    per_head_cost DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    allergy_charges DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    services_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    delivery_charge DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    vat DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    grand_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    payment_method ENUM('cash_on_delivery','cash_pickup','advance_payment') DEFAULT 'cash_on_delivery',
    promo_code VARCHAR(50) DEFAULT NULL,
    promo_discount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('pending','confirmed','preparing','completed','cancelled','refunded') DEFAULT 'pending',
    invoice_path VARCHAR(255) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- BOOKING MENU SELECTIONS
-- ============================================
CREATE TABLE IF NOT EXISTS booking_menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    menu_item_id INT DEFAULT NULL,
    item_name VARCHAR(150) NOT NULL,
    item_category VARCHAR(50) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    spice_level VARCHAR(20) DEFAULT NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================
-- BOOKING ALLERGIES
-- ============================================
CREATE TABLE IF NOT EXISTS booking_allergies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    allergy_type VARCHAR(50) NOT NULL,
    guest_count INT NOT NULL DEFAULT 0,
    extra_charge DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- BOOKING ADDITIONAL SERVICES
-- ============================================
CREATE TABLE IF NOT EXISTS booking_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    service_name VARCHAR(150) NOT NULL,
    price DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- TESTIMONIALS
-- ============================================
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    event_type VARCHAR(100) DEFAULT NULL,
    rating TINYINT NOT NULL DEFAULT 5,
    review TEXT NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- GALLERY
-- ============================================
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) NOT NULL DEFAULT 'general',
    image_path VARCHAR(255) NOT NULL,
    caption VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- OFFERS / PROMO CODES
-- ============================================
CREATE TABLE IF NOT EXISTS offers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    type ENUM('percentage','fixed') NOT NULL DEFAULT 'percentage',
    value DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    min_order DECIMAL(10,2) DEFAULT 0.00,
    valid_from DATE DEFAULT NULL,
    valid_to DATE DEFAULT NULL,
    max_uses INT DEFAULT NULL,
    used_count INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- SEED DATA
-- ============================================

-- Default admin user (password: admin123)
INSERT INTO users_admin (username, password_hash, email, full_name) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@zamahi.co.uk', 'Admin User');

-- ============================================
-- MENU ITEMS SEED DATA
-- ============================================

-- Proteins
INSERT INTO menu_items (category, sub_category, name, description, price, sort_order) VALUES
('protein', 'Chicken', 'Red Masala Chicken', 'Succulent chicken in rich red masala sauce', 0.00, 1),
('protein', 'Chicken', 'White Korma Chicken', 'Creamy white korma with aromatic spices', 0.00, 2),
('protein', 'Chicken', 'Grilled Chicken', 'Perfectly grilled chicken with herbs', 0.00, 3),
('protein', 'Lamb', 'Lamb Karahi', 'Tender lamb cooked in traditional karahi', 0.00, 4),
('protein', 'Lamb', 'Lamb Biryani Style', 'Aromatic lamb with biryani spices', 0.00, 5),
('protein', 'Lamb', 'Roasted Lamb', 'Slow-roasted lamb with rosemary', 0.00, 6),
('protein', 'Beef', 'Beef Nihari', 'Slow-cooked beef nihari with rich gravy', 0.00, 7),
('protein', 'Beef', 'Beef Keema', 'Minced beef with peas and spices', 0.00, 8),
('protein', 'BBQ', 'BBQ Mixed Grill', 'Selection of grilled meats with BBQ glaze', 0.00, 9),
('protein', 'BBQ', 'BBQ Seekh Kebab', 'Chargrilled minced meat kebabs', 0.00, 10),
('protein', 'Vegetarian', 'Paneer Tikka Masala', 'Cottage cheese in tikka sauce', 0.00, 11),
('protein', 'Vegetarian', 'Mixed Vegetable Curry', 'Seasonal vegetables in aromatic curry', 0.00, 12),
('protein', 'Vegan', 'Vegan Chickpea Curry', 'Hearty chickpea curry with coconut', 0.00, 13),
('protein', 'Vegan', 'Vegan Lentil Daal', 'Rich lentil daal with cumin tempering', 0.00, 14);

-- Rice
INSERT INTO menu_items (category, name, description, price, sort_order) VALUES
('rice', 'Basmati Rice', 'Premium long-grain basmati rice', 0.00, 1),
('rice', 'Pilau Rice', 'Fragrant pilau rice with whole spices', 0.00, 2),
('rice', 'Veg Rice', 'Flavorful basmati rice with vegetables', 0.00, 3),
('rice', 'Chicken Rice', 'Aromatic chicken-infused basmati rice', 0.00, 4);

-- Bread
INSERT INTO menu_items (category, name, description, price, sort_order) VALUES
('bread', 'Naan', 'Freshly baked tandoori naan', 0.00, 1),
('bread', 'Roti', 'Traditional handmade roti', 0.00, 2),
('bread', 'Kulcha', 'Leavened Indian bread', 0.00, 3);

-- Sides (also used for Salad and Sauce)
INSERT INTO menu_items (category, name, description, price, sort_order) VALUES
('sides', 'Garden Salad', 'Fresh seasonal garden salad', 0.00, 1),
('sides', 'Raita', 'Cool yoghurt with cucumber and mint', 0.00, 2),
('sides', 'Special Sauce', 'House special dipping sauce', 0.00, 3),
('sides', 'Green Salad', 'Fresh mixed greens with house dressing', 0.00, 4),
('sides', 'Pickle/Chutney', 'Assorted pickles and chutneys', 0.00, 5),
('sides', 'Mint Sauce', 'Refreshing mint yogurt sauce', 0.00, 6),
('sides', 'Tamarind Chutney', 'Sweet and tangy tamarind sauce', 0.00, 7),
('sides', 'Green Chutney', 'Fresh coriander and mint chutney', 0.00, 8);

-- Desserts
INSERT INTO menu_items (category, name, description, price, sort_order) VALUES
('desserts', 'Gulab Jamun', 'Golden dumplings in rose syrup', 0.00, 1),
('desserts', 'Kheer', 'Creamy rice pudding with cardamom', 0.00, 2),
('desserts', 'Brownies', 'Rich chocolate brownies', 0.00, 3),
('desserts', 'Cheesecake', 'New York style cheesecake', 0.00, 4);

-- Starters (add-ons with extra charge)
INSERT INTO menu_items (category, name, description, price, is_addon, sort_order) VALUES
('starters', 'Chicken Pakora', 'Crispy spiced chicken fritters', 3.50, 1, 1),
('starters', 'Soup & Crackers', 'Seasonal soup with artisan crackers', 2.50, 1, 2),
('starters', 'Fish Bites', 'Golden fried fish bites with tartar', 4.00, 1, 3),
('starters', 'Fresh Juice', 'Freshly squeezed seasonal juice', 2.00, 1, 4);

-- Drinks
INSERT INTO menu_items (category, name, description, price, sort_order) VALUES
('drinks', 'Water', 'Still mineral water', 0.00, 1),
('drinks', 'Soft Drinks', 'Selection of soft drinks', 1.50, 2),
('drinks', 'Mocktails', 'Premium non-alcoholic cocktails', 3.00, 3);

-- ============================================
-- TESTIMONIALS SEED DATA
-- ============================================
INSERT INTO testimonials (name, event_type, rating, review) VALUES
('Sarah & James Thompson', 'Wedding', 5, 'ZAMAHI catered our wedding and it was absolutely incredible. The food was exquisite, presentation was flawless, and the service was impeccable. Our guests are still talking about it!'),
('David Chen', 'Corporate', 5, 'We hired ZAMAHI for our annual corporate gala. The team was professional, the menu was perfectly curated, and the live cooking station was a huge hit with our clients.'),
('Priya Patel', 'Private Party', 5, 'Amazing catering for our anniversary celebration. The attention to detail was remarkable — from the starter platters to the dessert spread. Truly a luxury experience.'),
('Mark Williams', 'Wedding', 5, 'From the initial consultation to the last plate served, ZAMAHI exceeded every expectation. The lamb dishes were out of this world. Highly recommended!'),
('Aisha Khan', 'Private Party', 4, 'Beautiful presentation and delicious food for my daughters baby shower. The team accommodated all dietary requirements perfectly. Will definitely book again.');

-- ============================================
-- OFFERS SEED DATA
-- ============================================
INSERT INTO offers (code, description, type, value, min_order, valid_from, valid_to) VALUES
('WELCOME10', 'Welcome offer – 10% off your first booking', 'percentage', 10.00, 500.00, '2026-01-01', '2026-12-31'),
('SUMMER2026', 'Summer special – £50 off orders over £1000', 'fixed', 50.00, 1000.00, '2026-06-01', '2026-08-31');
