<?php
/**
 * DB Migration: Support Extended Invoices and Non-Registered Clients
 */
require_once 'includes/db_connect.php';

try {
    // 1. Add non-registered client fields to invoices table
    $pdo->exec("ALTER TABLE invoices 
        ADD COLUMN IF NOT EXISTS guest_name VARCHAR(100) DEFAULT NULL AFTER user_id,
        ADD COLUMN IF NOT EXISTS guest_email VARCHAR(100) DEFAULT NULL AFTER guest_name,
        ADD COLUMN IF NOT EXISTS guest_phone VARCHAR(20) DEFAULT NULL AFTER guest_email,
        ADD COLUMN IF NOT EXISTS subtotal DECIMAL(10,2) DEFAULT 0.00 AFTER amount,
        ADD COLUMN IF NOT EXISTS tax_amount DECIMAL(10,2) DEFAULT 0.00 AFTER subtotal,
        ADD COLUMN IF NOT EXISTS discount_amount DECIMAL(10,2) DEFAULT 0.00 AFTER tax_amount,
        ADD COLUMN IF NOT EXISTS terms_payment VARCHAR(255) DEFAULT '70% Prior, 30% Upon Delivery' AFTER due_date
    ");
    
    // 2. Create invoice_items table for granular billing
    $pdo->exec("CREATE TABLE IF NOT EXISTS invoice_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        invoice_id INT NOT NULL,
        description TEXT NOT NULL,
        quantity DECIMAL(10,2) NOT NULL DEFAULT 1.00,
        unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        total_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    echo "Billing migration completed successfully.\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
