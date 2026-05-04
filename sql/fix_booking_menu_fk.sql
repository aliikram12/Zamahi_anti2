-- ============================================
-- ZAMAHI - Fix Foreign Key Constraint Issues
-- Run this script to resolve FK violations
-- ============================================

USE zamahi_catering;

-- ============================================
-- STEP 1: Drop existing FK constraint
-- ============================================
ALTER TABLE booking_menu DROP FOREIGN KEY IF EXISTS booking_menu_ibfk_2;

-- ============================================
-- STEP 2: Modify column to allow NULL
-- ============================================
ALTER TABLE booking_menu MODIFY menu_item_id INT NULL;

-- ============================================
-- STEP 3: Re-add FK with SET NULL on delete
-- This allows booking_menu records to exist 
-- even if menu_item is deleted
-- ============================================
ALTER TABLE booking_menu 
ADD CONSTRAINT booking_menu_ibfk_2 
FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- ============================================
-- STEP 4: Verify menu_items exist
-- ============================================
SELECT 'Checking menu_items table...' AS status;
SELECT COUNT(*) AS total_items FROM menu_items;

-- ============================================
-- STEP 5: Fix orphaned booking_menu records
-- ============================================
UPDATE booking_menu 
SET menu_item_id = NULL 
WHERE menu_item_id IS NOT NULL 
AND menu_item_id NOT IN (SELECT id FROM menu_items);

SELECT 'Orphaned records fixed' AS status;

-- ============================================
-- STEP 6: Show current menu items for verification
-- ============================================
SELECT id, name, category, is_active 
FROM menu_items 
ORDER BY category, sort_order;

SELECT 'Foreign key fix complete!' AS result;
