<?php
/**
 * API: Ticket Conversation Details
 * Fetches the initial message and all replies for a specific ticket.
 */

header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$ticket_ref = $_GET['ref'] ?? '';
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if (empty($ticket_ref)) {
    echo json_encode(['success' => false, 'message' => 'Ticket reference required.']);
    exit;
}

ob_start();

try {
    // 1. Get Ticket Info
    if ($is_admin) {
        $stmt = $pdo->prepare("SELECT t.id, t.ticket_ref, t.subject, t.message, t.status, t.priority, t.created_at, u.full_name as client_name, u.email as client_email 
                               FROM tickets t 
                               JOIN users u ON t.user_id = u.id 
                               WHERE t.ticket_ref = ?");
        $stmt->execute([$ticket_ref]);
    } else {
        $stmt = $pdo->prepare("SELECT id, ticket_ref, subject, message, status, priority, created_at 
                               FROM tickets 
                               WHERE ticket_ref = ? AND user_id = ?");
        $stmt->execute([$ticket_ref, $user_id]);
    }
    
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Ticket not found.']);
        exit;
    }

    // 2. Get Replies
    $stmt = $pdo->prepare("SELECT r.message, r.is_admin_reply, r.created_at, u.full_name as author_name 
                           FROM ticket_replies r 
                           JOIN users u ON r.user_id = u.id 
                           WHERE r.ticket_id = ? 
                           ORDER BY r.created_at ASC");
    $stmt->execute([$ticket['id']]);
    $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ob_clean();
    echo json_encode([
        'success' => true, 
        'ticket' => $ticket, 
        'replies' => $replies
    ]);
} catch (PDOException $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
