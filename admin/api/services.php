<?php
/**
 * API: Services (Subscriptions) Management
 */
header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    if ($method === 'GET') {
        $stmt = $pdo->query("
            SELECT s.*, u.full_name as client_name, p.name as product_name 
            FROM services s 
            JOIN users u ON s.user_id = u.id 
            LEFT JOIN products p ON s.product_id = p.id 
            ORDER BY s.created_at DESC
        ");
        $services = $stmt->fetchAll();
        echo json_encode(['success' => true, 'services' => $services]);
    } 
    elseif ($method === 'POST') {
        $action = $input['action'] ?? 'update_status';
        $id = $input['id'] ?? null;
        if (!$id) throw new Exception('Service ID is missing.');

        if ($action === 'update_status') {
            $status = $input['status'] ?? 'active';
            $stmt = $pdo->prepare("UPDATE services SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            echo json_encode(['success' => true, 'message' => 'Service status updated to ' . $status]);
        }
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
