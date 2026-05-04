<?php
require_once 'includes/config.php';
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "ALTER TABLE bookings 
            ADD COLUMN amount_paid DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            ADD COLUMN payment_percentage INT NOT NULL DEFAULT 0,
            ADD COLUMN transaction_id VARCHAR(100) DEFAULT NULL,
            ADD COLUMN payment_status VARCHAR(50) DEFAULT 'unpaid'
            AFTER payment_method";
    
    $pdo->exec($sql);
    echo "Database updated successfully!";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Columns already exist.";
    } else {
        die("DB Error: " . $e->getMessage());
    }
}
?>
