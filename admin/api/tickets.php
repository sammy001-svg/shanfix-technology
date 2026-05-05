<?php
/**
 * API: Admin Ticket Management
 * Shanfix Technology - Modernized Logic
 */

header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();

// Secure Admin Authorization
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized administrator access.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
ob_start();

if ($method === 'GET') {
    $ref = $_GET['ref'] ?? '';

    try {
        if ($ref) {
            // Fetch specific ticket details (Conversation View)
            $stmt = $pdo->prepare("SELECT t.*, u.full_name as client_name, u.email as client_email 
                                 FROM tickets t 
                                 JOIN users u ON t.user_id = u.id 
                                 WHERE t.ticket_ref = ?");
            $stmt->execute([$ref]);
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$ticket) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Ticket not found.']);
                exit;
            }

            // Fetch replies
            $stmt = $pdo->prepare("SELECT r.*, u.full_name as author_name 
                                 FROM ticket_replies r 
                                 JOIN users u ON r.user_id = u.id 
                                 WHERE r.ticket_id = ? 
                                 ORDER BY r.created_at ASC");
            $stmt->execute([$ticket['id']]);
            $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            ob_clean();
            echo json_encode(['success' => true, 'ticket' => $ticket, 'replies' => $replies]);
        } else {
            // List all tickets for dashboard overview
            $stmt = $pdo->query("SELECT t.id, t.ticket_ref, t.subject, t.priority, t.status, t.created_at, u.email as clientEmail, u.full_name as clientName 
                                 FROM tickets t 
                                 JOIN users u ON t.user_id = u.id 
                                 ORDER BY t.created_at DESC");
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            ob_clean();
            echo json_encode(['success' => true, 'tickets' => $tickets]);
        }
    } catch (PDOException $e) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Query failure: ' . $e->getMessage()]);
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
            echo json_encode(['success' => true, 'message' => 'Ticket has been marked as resolved.']);
        } catch (PDOException $e) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Operation failed.']);
        }
    } elseif ($action === 'add_reply') {
        $message = trim($input['message'] ?? '');
        if (empty($message) || empty($ticket_id)) {
            echo json_encode(['success' => false, 'message' => 'Message and Ticket ID are required.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO ticket_replies (ticket_id, user_id, message, is_admin_reply) VALUES (?, ?, ?, 1)");
            $stmt->execute([$ticket_id, $_SESSION['user_id'], $message]);
            
            // Auto-update ticket status to 'replied' or keep 'open'
            $pdo->prepare("UPDATE tickets SET status = 'replied' WHERE id = ?")->execute([$ticket_id]);

            ob_clean();
            echo json_encode(['success' => true, 'message' => 'Reply sent successfully.']);
        } catch (PDOException $e) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Reply failed: ' . $e->getMessage()]);
        }
    }
}
