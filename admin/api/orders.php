<?php
/**
 * API: Order Management
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
        $stmt = $pdo->query("SELECT o.*, u.full_name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
        $orders = $stmt->fetchAll();
        
        // Fetch items for each order
        foreach ($orders as &$order) {
            $stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $stmtItems->execute([$order['id']]);
            $order['items'] = $stmtItems->fetchAll();
        }
        
        echo json_encode(['success' => true, 'orders' => $orders]);
    } 
    elseif ($method === 'POST') {
        $action = $input['action'] ?? 'update_status';
        $id = $input['id'] ?? null;
        
        if (!$id) throw new Exception('Order ID is missing.');

        if ($action === 'update_status') {
            $status = $input['status'] ?? 'pending';
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            echo json_encode(['success' => true, 'message' => 'Order status updated to ' . $status]);
        }
        elseif ($action === 'send_reminder') {
            $stmt = $pdo->prepare("UPDATE orders SET is_reminded = 1 WHERE id = ?");
            $stmt->execute([$id]);
            
            // Mock reminder logic (logging for now)
            error_log("REMINDER SENT for Order #$id to client.");
            
            echo json_encode(['success' => true, 'message' => 'Reminder sent to client successfully!']);
        }
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
