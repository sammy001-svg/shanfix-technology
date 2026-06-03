<?php
/**
 * API: Contact Messages Inbox
 */

header('Content-Type: application/json');
require_once '../../includes/db_connect.php';
require_once '../../includes/mailer.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// GET — list messages or unread count
if ($method === 'GET') {
    try {
        if (isset($_GET['unread_count'])) {
            $c = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'")->fetchColumn();
            echo json_encode(['success' => true, 'count' => (int)$c]);
        } else {
            $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
            echo json_encode(['success' => true, 'messages' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fetch failed.']);
    }
    exit;
}

// POST — mark_read or reply
if ($method === 'POST') {
    $input  = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    $id     = (int)($input['id'] ?? 0);

    if ($action === 'mark_read') {
        try {
            $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ? AND status = 'unread'")->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    if ($action === 'reply') {
        $reply = trim($input['reply'] ?? '');
        if (empty($reply)) {
            echo json_encode(['success' => false, 'message' => 'Reply message is required.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT name, email, subject FROM contact_messages WHERE id = ?");
            $stmt->execute([$id]);
            $msg = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$msg) {
                echo json_encode(['success' => false, 'message' => 'Message not found.']);
                exit;
            }

            $ok = Mailer::contactReply($msg['name'], $msg['email'], $msg['subject'], $reply);

            $pdo->prepare("UPDATE contact_messages SET status = 'replied', reply_message = ?, replied_at = NOW() WHERE id = ?")
                ->execute([$reply, $id]);

            echo json_encode(['success' => true, 'message' => $ok ? 'Reply sent.' : 'Saved but email delivery failed — check mail config.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error.']);
        }
        exit;
    }

    if ($action === 'delete') {
        try {
            $pdo->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}
