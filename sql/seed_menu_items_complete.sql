-- ============================================
-- ZAMAHI - Complete Menu Items Seed Script
-- Run this to ensure all menu items exist
-- ============================================

USE zamahi_catering;

-- ============================================
-- PROTEIN DISHES
-- ============================================
INSERT IGNORE INTO menu_items (category, sub_category, name, description, price, is_active, sort_order) VALUES
-- Chicken
('protein', 'Chicken', 'Red Masala Chicken', 'Succulent chicken in rich red masala sauce', 0.00, 1, 1),
('protein', 'Chicken', 'White Korma Chicken', 'Creamy white korma with aromatic spices', 0.00, 1, 2),
('protein', 'Chicken', 'Grilled Chicken', 'Perfectly grilled chicken with herbs', 0.00, 1, 3),
-- Lamb
('protein', 'Lamb', 'Lamb Karahi', 'Tender lamb cooked in traditional karahi', 0.00, 1, 4),
('protein', 'Lamb', 'Lamb Biryani Style', 'Aromatic lamb with biryani spices', 0.00, 1, 5),
('protein', 'Lamb', 'Roasted Lamb', 'Slow-roasted lamb with rosemary', 0.00, 1, 6),
-- Beef
('protein', 'Beef', 'Beef Nihari', 'Slow-cooked beef nihari with rich gravy', 0.00, 1, 7),
('protein', 'Beef', 'Beef Keema', 'Minced beef with peas and spices', 0.00, 1, 8),
-- BBQ
('protein', 'BBQ', 'BBQ Mixed Grill', 'Selection of grilled meats with BBQ glaze', 0.00, 1, 9),
('protein', 'BBQ', 'BBQ Seekh Kebab', 'Chargrilled minced meat kebabs', 0.00, 1, 10),
-- Vegetarian
('protein', 'Vegetarian', 'Paneer Tikka Masala', 'Cottage cheese in tikka sauce', 0.00, 1, 11),
('protein', 'Vegetarian', 'Mixed Vegetable Curry', 'Seasonal vegetables in aromatic curry', 0.00, 1, 12),
-- Vegan
('protein', 'Vegan', 'Vegan Chickpea Curry', 'Hearty chickpea curry with coconut', 0.00, 1, 13),
('protein', 'Vegan', 'Vegan Lentil Daal', 'Rich lentil daal with cumin tempering', 0.00, 1, 14);

-- ============================================
-- RICE ITEMS
-- ============================================
INSERT IGNORE INTO menu_items (category, name, description, price, is_active, sort_order) VALUES
('rice', 'Basmati Rice', 'Premium long-grain basmati rice', 0.00, 1, 1),
('rice', 'Pilau Rice', 'Fragrant pilau rice with whole spices', 0.00, 1, 2),
('rice', 'Veg Rice', 'Flavorful basmati rice with vegetables', 0.00, 1, 3),
('rice', 'Chicken Rice', 'Aromatic chicken-infused basmati rice', 0.00, 1, 4);

-- ============================================
-- BREAD ITEMS
-- ============================================
INSERT IGNORE INTO menu_items (category, name, description, price, is_active, sort_order) VALUES
('bread', 'Naan', 'Freshly baked tandoori naan', 0.00, 1, 1),
('bread', 'Roti', 'Traditional handmade roti', 0.00, 1, 2),
('bread', 'Kulcha', 'Leavened Indian bread', 0.00, 1, 3);

-- ============================================
-- SALAD ITEMS (using 'sides' category)
-- ============================================
INSERT IGNORE INTO menu_items (category, name, description, price, is_active, sort_order) VALUES
('sides', 'Garden Salad', 'Fresh seasonal garden salad', 0.00, 1, 1),
('sides', 'Green Salad', 'Fresh mixed greens with house dressing', 0.00, 1, 2),
('sides', 'Raita', 'Cool yoghurt with cucumber and mint', 0.00, 1, 3),
('sides', 'Pickle/Chutney', 'Assorted pickles and chutneys', 0.00, 1, 4);

-- ============================================
-- SAUCE ITEMS (using 'sides' category)
-- ============================================
INSERT IGNORE INTO menu_items (category, name, description, price, is_active, sort_order) VALUES
('sides', 'Special Sauce', 'House special dipping sauce', 0.00, 1, 5),
('sides', 'Mint Sauce', 'Refreshing mint yogurt sauce', 0.00, 1, 6),
('sides', 'Tamarind Chutney', 'Sweet and tangy tamarind sauce', 0.00, 1, 7),
('sides', 'Green Chutney', 'Fresh coriander and mint chutney', 0.00, 1, 8);

-- ============================================
-- DESSERTS
-- ============================================
INSERT IGNORE INTO menu_items (category, name, description, price, is_active, sort_order) VALUES
('desserts', 'Gulab Jamun', 'Golden dumplings in rose syrup', 0.00, 1, 1),
('desserts', 'Kheer', 'Creamy rice pudding with cardamom', 0.00, 1, 2),
('desserts', 'Brownies', 'Rich chocolate brownies', 0.00, 1, 3),
('desserts', 'Cheesecake', 'New York style cheesecake', 0.00, 1, 4);

-- ============================================
-- STARTERS (Add-ons with extra charge)
-- ============================================
INSERT IGNORE INTO menu_items (category, name, description, price, is_addon, is_active, sort_order) VALUES
('starters', 'Chicken Pakora', 'Crispy spiced chicken fritters', 3.50, 1, 1, 1),
('starters', 'Soup & Crackers', 'Seasonal soup with artisan crackers', 2.50, 1, 1, 2),
('starters', 'Fish Bites', 'Golden fried fish bites with tartar', 4.00, 1, 1, 3),
('starters', 'Fresh Juice', 'Freshly squeezed seasonal juice', 2.00, 1, 1, 4);

-- ============================================
-- DRINKS
-- ============================================
INSERT IGNORE INTO menu_items (category, name, description, price, is_active, sort_order) VALUES
('drinks', 'Water', 'Still mineral water', 0.00, 1, 1),
('drinks', 'Soft Drinks', 'Selection of soft drinks', 1.50, 1, 2),
('drinks', 'Mocktails', 'Premium non-alcoholic cocktails', 3.00, 1, 3);

-- ============================================
-- VERIFY
-- ============================================
SELECT 'Menu items seeded successfully!' AS message;
SELECT category, COUNT(*) as total FROM menu_items WHERE is_active = 1 GROUP BY category;
