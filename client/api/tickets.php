<?php
/**
 * API: Client Support Tickets
 * Shanfix Technology - Database Persistence
 */

header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

// Manage Output Buffer
ob_start();

if ($method === 'GET') {
    // 1. Fetch tickets for the logged-in user
    try {
        $stmt = $pdo->prepare("SELECT ticket_ref as id, subject, priority, status, created_at as date, message FROM tickets WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_clean();
        echo json_encode(['success' => true, 'tickets' => $tickets]);
    } catch (PDOException $e) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Failed to fetch tickets.']);
    }
} elseif ($method === 'POST') {
    // 2. Create a new ticket
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) $input = $_POST;

    $subject = trim($input['subject'] ?? '');
    $priority = trim($input['priority'] ?? 'medium');
    $message = trim($input['message'] ?? '');

    if (empty($subject) || empty($message)) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Subject and message are required.']);
        exit;
    }

    try {
        $ticket_ref = 'SFT-' . strtoupper(substr(uniqid(), -4)) . rand(100, 999);
        
        $stmt = $pdo->prepare("INSERT INTO tickets (user_id, ticket_ref, subject, priority, status, message) VALUES (?, ?, ?, ?, 'open', ?)");
        $result = $stmt->execute([$user_id, $ticket_ref, $subject, $priority, $message]);

        if ($result) {
            ob_clean();
            echo json_encode(['success' => true, 'message' => 'Ticket created successfully!', 'ticket_ref' => $ticket_ref]);
        } else {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Failed to log ticket.']);
        }
    } catch (PDOException $e) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
