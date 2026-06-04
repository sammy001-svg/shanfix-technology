<?php
/**
 * API: Admin Product Management
 * Enhanced to support multiple images.
 */

header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'GET' && (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
ob_start();

try {
    if ($method === 'GET') {
        // Fetch products with their category and primary image
        $stmt = $pdo->query("SELECT p.*, c.name as category_name 
                             FROM products p 
                             JOIN categories c ON p.category_id = c.id 
                             ORDER BY c.name ASC, p.name ASC");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch additional images for each product
        foreach ($products as &$p) {
            $imgStmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ?");
            $imgStmt->execute([$p['id']]);
            $p['additional_images'] = $imgStmt->fetchAll(PDO::FETCH_COLUMN);
        }
        
        ob_clean();
        echo json_encode(['success' => true, 'products' => $products]);
        exit;
    } 

    if ($method === 'POST') {
        $action = $_POST['action'] ?? 'create';
        $id = $_POST['id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $category_id = $_POST['category_id'] ?? '';
        $price = $_POST['price'] ?? 0;
        $description = trim($_POST['description'] ?? '');
        $features = trim($_POST['features'] ?? '');
        $status = $_POST['status'] ?? 'active';
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $existing_primary = $_POST['existing_image'] ?? '';

        if (empty($name) || empty($category_id)) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Name and category are required.']);
            exit;
        }

        $target_dir = "../../assets/uploads/products/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

        // Handle Primary Image
        $image_url = $existing_primary;
        if (isset($_FILES['primary_image']) && $_FILES['primary_image']['error'] === 0) {
            $file_ext = pathinfo($_FILES["primary_image"]["name"], PATHINFO_EXTENSION);
            $filename = uniqid('p_') . '.' . $file_ext;
            if (move_uploaded_file($_FILES["primary_image"]["tmp_name"], $target_dir . $filename)) {
                $image_url = 'assets/uploads/products/' . $filename;
            }
        }

        // Auto-add any missing optional columns (handles older databases)
        $colCheck = $pdo->query(
            "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products'
             AND COLUMN_NAME IN ('image_url', 'features', 'is_featured')"
        )->fetchAll(PDO::FETCH_COLUMN);

        if (!in_array('image_url', $colCheck)) {
            $pdo->exec("ALTER TABLE `products` ADD COLUMN `image_url` varchar(255) DEFAULT NULL AFTER `price`");
            $colCheck[] = 'image_url';
        }
        if (!in_array('features', $colCheck)) {
            $pdo->exec("ALTER TABLE `products` ADD COLUMN `features` text DEFAULT NULL AFTER `description`");
            $colCheck[] = 'features';
        }
        if (!in_array('is_featured', $colCheck)) {
            $pdo->exec("ALTER TABLE `products` ADD COLUMN `is_featured` tinyint(1) DEFAULT 0 AFTER `image_url`");
            $colCheck[] = 'is_featured';
        }

        $hasImg      = in_array('image_url',   $colCheck);
        $hasFeatures = in_array('features',    $colCheck);
        $hasFeatured = in_array('is_featured', $colCheck);

        // Build column/value lists dynamically
        $cols   = ['category_id', 'name', 'description', 'price', 'status'];
        $vals   = [$category_id,   $name,  $description,  $price,  $status];
        if ($hasFeatures) { $cols[] = 'features';    $vals[] = $features; }
        if ($hasImg)      { $cols[] = 'image_url';   $vals[] = $image_url; }
        if ($hasFeatured) { $cols[] = 'is_featured'; $vals[] = $is_featured; }

        if ($action === 'create') {
            $placeholders = implode(', ', array_fill(0, count($cols), '?'));
            $colList      = implode(', ', $cols);
            $stmt = $pdo->prepare("INSERT INTO products ($colList) VALUES ($placeholders)");
            $stmt->execute($vals);
            $id  = $pdo->lastInsertId();
            $msg = 'Product created successfully.';
        } else {
            $setParts = array_map(fn($c) => "$c = ?", $cols);
            $setClause = implode(', ', $setParts);
            $vals[] = $id;
            $stmt = $pdo->prepare("UPDATE products SET $setClause WHERE id = ?");
            $stmt->execute($vals);
            $msg = 'Product updated successfully.';
        }

        // Handle Additional Images
        if (isset($_FILES['additional_images'])) {
            foreach ($_FILES['additional_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['additional_images']['error'][$key] === 0) {
                    $file_ext = pathinfo($_FILES["additional_images"]["name"][$key], PATHINFO_EXTENSION);
                    $filename = uniqid('p_sub_') . '.' . $file_ext;
                    if (move_uploaded_file($tmp_name, $target_dir . $filename)) {
                        $img_path = 'assets/uploads/products/' . $filename;
                        $pdo->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)")->execute([$id, $img_path]);
                    }
                }
            }
        }

        ob_clean();
        echo json_encode(['success' => true, 'message' => $msg, 'id' => $id]);
        exit;
    }

    if ($method === 'DELETE' || isset($_GET['action']) && $_GET['action'] === 'delete') {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            ob_clean();
            echo json_encode(['success' => true, 'message' => 'Product deleted.']);
            exit;
        }
    }

} catch (PDOException $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
