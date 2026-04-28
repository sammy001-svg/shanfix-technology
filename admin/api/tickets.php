<?php
/**
 * API: Admin Ticket Management
 */

header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();

// In a real app, check for admin session
// $_SESSION['role'] === 'admin' check should be here
// For now, we assume the user is authorized if they reach this file

$method = $_SERVER['REQUEST_METHOD'];
ob_start();

if ($method === 'GET') {
    // List all tickets with client info
    try {
        $stmt = $pdo->query("SELECT t.id, t.ticket_ref, t.subject, t.priority, t.status, t.created_at, u.email as clientEmail 
                             FROM tickets t 
                             JOIN users u ON t.user_id = u.id 
                             ORDER BY t.created_at DESC");
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        ob_clean();
        echo json_encode(['success' => true, 'tickets' => $tickets]);
    } catch (PDOException $e) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Failed to fetch tickets.']);
    }
} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) $input = $_POST;

    $action = $input['action'] ?? '';
    $ticket_id = $input['ticket_id'] ?? '';

    if ($action === 'close') {
        try {
            $stmt = $pdo->prepare("UPDATE tickets SET status = 'closed' WHERE id = ?");
            $stmt->execute([$ticket_id]);
            ob_clean();
            echo json_encode(['success' => true, 'message' => 'Ticket closed.']);
        } catch (PDOException $e) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Failed to close ticket.']);
        }
    }
}
