<?php
/**
 * DB Migration: Support Multiple Images and Category Thumbnails
 */
require_once 'includes/db_connect.php';

try {
    // 1. Add image_url to categories table
    $pdo->exec("ALTER TABLE categories ADD COLUMN IF NOT EXISTS image_url VARCHAR(255) DEFAULT NULL AFTER description");
    
    // 2. Create product_images table for multi-image support
    $pdo->exec("CREATE TABLE IF NOT EXISTS product_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        image_url VARCHAR(255) NOT NULL,
        is_primary TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    echo "Database migrated successfully.\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
