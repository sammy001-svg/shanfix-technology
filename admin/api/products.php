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
        // Support both multipart/form-data (file uploads) and application/json (delete, etc.)
        $jsonInput = json_decode(file_get_contents('php://input'), true);

        $action = $jsonInput['action'] ?? $_POST['action'] ?? 'create';
        $id     = $jsonInput['id']     ?? $_POST['id']     ?? null;

        // Handle delete via JSON POST
        if ($action === 'delete') {
            if (!$id) { ob_clean(); echo json_encode(['success' => false, 'message' => 'Product ID required.']); exit; }
            try {
                $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
                ob_clean();
                echo json_encode(['success' => true, 'message' => 'Product deleted.']);
            } catch (PDOException $e) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $e->getMessage()]);
            }
            exit;
        }

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

        $runProductQuery = function() use ($pdo, $action, &$id, $category_id, $name, $description, $features, $price, $image_url, $is_featured, $status) {
            if ($action === 'create') {
                $stmt = $pdo->prepare(
                    "INSERT INTO products (category_id, name, description, features, price, image_url, is_featured, status)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([$category_id, $name, $description, $features, $price, $image_url, $is_featured, $status]);
                $id = (int)$pdo->lastInsertId();
            } else {
                $stmt = $pdo->prepare(
                    "UPDATE products SET category_id=?, name=?, description=?, features=?, price=?, image_url=?, is_featured=?, status=? WHERE id=?"
                );
                $stmt->execute([$category_id, $name, $description, $features, $price, $image_url, $is_featured, $status, $id]);
            }
        };

        try {
            $runProductQuery();
        } catch (PDOException $col_err) {
            // If a column is missing, auto-add all optional columns and retry once
            if ($col_err->getCode() === '42S22' || strpos($col_err->getMessage(), '1054') !== false) {
                $pdo->exec("ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `image_url` varchar(255) DEFAULT NULL AFTER `price`");
                $pdo->exec("ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `features` text DEFAULT NULL AFTER `description`");
                $pdo->exec("ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `is_featured` tinyint(1) DEFAULT 0 AFTER `image_url`");
                $runProductQuery();
            } else {
                throw $col_err;
            }
        }

        $msg = $action === 'create' ? 'Product created successfully.' : 'Product updated successfully.';

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
