<?php
require_once 'includes/db_connect.php';

try {
    $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS features TEXT AFTER description");
    echo "Successfully added 'features' column to 'products' table.";
} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage();
}
?>
