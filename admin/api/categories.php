<?php
/**
 * API: Admin Category Management
 * Updated to support image uploads and structured updates.
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
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ob_clean();
        echo json_encode(['success' => true, 'categories' => $categories]);
        exit;
    }

    if ($method === 'POST') {
        $action = $_POST['action'] ?? 'create';
        $id = $_POST['id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $existing_image = $_POST['existing_image'] ?? '';

        if (empty($name)) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Category name is required.']);
            exit;
        }

        $slug = preg_replace('/[^a-z0-9\-]/', '', strtolower(str_replace(' ', '-', $name)));
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        // Image Handling
        $image_url = $existing_image;
        $target_dir = "../../assets/uploads/categories/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $file_ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $filename = uniqid('cat_') . '.' . $file_ext;
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $filename)) {
                $image_url = 'assets/uploads/categories/' . $filename;
            }
        }

        // Check whether image_url column exists (handles databases migrated before the column was added)
        $hasImageCol = (bool)$pdo->query(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'categories' AND COLUMN_NAME = 'image_url'"
        )->fetchColumn();

        if (!$hasImageCol) {
            // Column missing — add it automatically
            $pdo->exec("ALTER TABLE `categories` ADD COLUMN `image_url` varchar(255) DEFAULT NULL AFTER `description`");
            $hasImageCol = true;
        }

        if ($action === 'create') {
            if ($hasImageCol) {
                $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, image_url) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $slug, $description, $image_url]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
                $stmt->execute([$name, $slug, $description]);
            }
            $msg = 'Category added successfully.';
        } else {
            if ($hasImageCol) {
                $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, image_url = ? WHERE id = ?");
                $stmt->execute([$name, $slug, $description, $image_url, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ? WHERE id = ?");
                $stmt->execute([$name, $slug, $description, $id]);
            }
            $msg = 'Category updated successfully.';
        }

        ob_clean();
        echo json_encode(['success' => true, 'message' => $msg]);
        exit;
    }

    if ($method === 'DELETE' || isset($_GET['action']) && $_GET['action'] === 'delete') {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? ($_GET['id'] ?? null);

        if (!$id) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Category ID required.']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        ob_clean();
        echo json_encode(['success' => true, 'message' => 'Category deleted.']);
        exit;
    }

} catch (PDOException $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
