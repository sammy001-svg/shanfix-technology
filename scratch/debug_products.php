<?php
require_once 'includes/db_connect.php';
$stmt = $pdo->query("SELECT id, name, image_url FROM products");
$products = $stmt->fetchAll();
echo json_encode($products, JSON_PRETTY_PRINT);
