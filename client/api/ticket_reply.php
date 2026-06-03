<?php
/**
 * API: Post Ticket Reply
 */

header('Content-Type: application/json');
require_once '../../includes/db_connect.php';
require_once '../../includes/mailer.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

$ticket_id = $input['ticket_id'] ?? '';
$message = trim($input['message'] ?? '');

if (empty($ticket_id) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Message is required.']);
    exit;
}

ob_start();

try {
    // Verify ownership if not admin
    if (!$is_admin) {
        $stmt = $pdo->prepare("SELECT id FROM tickets WHERE id = ? AND user_id = ?");
        $stmt->execute([$ticket_id, $user_id]);
        if (!$stmt->fetch()) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Access denied.']);
            exit;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO ticket_replies (ticket_id, user_id, message, is_admin_reply) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$ticket_id, $user_id, $message, $is_admin ? 1 : 0]);

    if ($result) {
        // Notify admin of client reply
        if (!$is_admin) {
            $tStmt = $pdo->prepare("SELECT t.ticket_ref, t.subject, u.full_name as client_name
                                    FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
            $tStmt->execute([$ticket_id]);
            $tRow = $tStmt->fetch(PDO::FETCH_ASSOC);
            $adminEmail = $_ENV['ADMIN_EMAIL'] ?? '';
            if ($tRow && $adminEmail) {
                Mailer::clientReplied($adminEmail, $tRow['client_name'], $tRow['ticket_ref'], $tRow['subject']);
            }
        }
        ob_clean();
        echo json_encode(['success' => true, 'message' => 'Reply posted successfully.']);
    } else {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Failed to post reply.']);
    }
} catch (PDOException $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
