<?php
/**
 * API: Admin Product Management
 * Handles CRUD operations for products including image uploads.
 */

header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();

// The GET method is public (for storefront), but POST/DELETE require auth
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
ob_start();

try {
    if ($method === 'GET') {
        // Fetch all products with category names
        $stmt = $pdo->query("SELECT p.*, c.name as category_name 
                             FROM products p 
                             JOIN categories c ON p.category_id = c.id 
                             ORDER BY p.created_at DESC");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        ob_clean();
        echo json_encode(['success' => true, 'products' => $products]);
        exit;
    } 

    if ($method === 'POST') {
        // Handle Create/Update
        $action = $_POST['action'] ?? 'create';
        $name = trim($_POST['name'] ?? '');
        $category_id = $_POST['category_id'] ?? '';
        $price = $_POST['price'] ?? 0;
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'active';
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $id = $_POST['id'] ?? null;

        if (empty($name) || empty($category_id)) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Name and category are required.']);
            exit;
        }

        // Image Handling
        $image_url = $_POST['existing_image'] ?? '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $target_dir = "../../uploads/products/";
            $file_ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $file_ext;
            $target_file = $target_dir . $filename;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = 'uploads/products/' . $filename;
            }
        }

        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, price, image_url, is_featured, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$category_id, $name, $description, $price, $image_url, $is_featured, $status]);
            $msg = 'Product created successfully.';
        } else {
            $stmt = $pdo->prepare("UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, image_url = ?, is_featured = ?, status = ? WHERE id = ?");
            $stmt->execute([$category_id, $name, $description, $price, $image_url, $is_featured, $status, $id]);
            $msg = 'Product updated successfully.';
        }

        ob_clean();
        echo json_encode(['success' => true, 'message' => $msg]);
        exit;
    }

    if ($method === 'DELETE' || (isset($_GET['action']) && $_GET['action'] === 'delete')) {
        $id = $_GET['id'] ?? '';
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
