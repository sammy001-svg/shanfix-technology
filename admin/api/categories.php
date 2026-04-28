<?php
/**
 * API: Admin Category Management
 */

header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();

// Public GET access, but other methods require auth
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && !isset($_SESSION['user_id'])) {
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
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) $input = $_POST;

        $name = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');
        $slug = strtolower(str_replace(' ', '-', $name));

        if (empty($name)) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Category name is required.']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
        $stmt->execute([$name, $slug, $description]);

        ob_clean();
        echo json_encode(['success' => true, 'message' => 'Category added successfully.']);
        exit;
    }

    if ($method === 'DELETE') {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;

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
