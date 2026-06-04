<?php
/**
 * API: Client Notifications
 * Returns unread counts: admin ticket replies + new (unpaid) invoices since last login.
 */
header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Unread admin replies: replies marked as admin (is_admin_reply=1) on tickets
    // owned by this user, created after the client's last session or last 30 days.
    $replyStmt = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM ticket_replies tr
        JOIN tickets t ON tr.ticket_id = t.id
        WHERE t.user_id = ?
          AND tr.is_admin_reply = 1
          AND tr.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    $replyStmt->execute([$user_id]);
    $unreadReplies = (int) $replyStmt->fetchColumn();

    // Unpaid invoices for this client
    $invStmt = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM invoices
        WHERE user_id = ? AND status = 'unpaid'
    ");
    $invStmt->execute([$user_id]);
    $unpaidInvoices = (int) $invStmt->fetchColumn();

    $total = $unreadReplies + $unpaidInvoices;

    echo json_encode([
        'success'        => true,
        'total'          => $total,
        'ticket_replies' => $unreadReplies,
        'unpaid_invoices'=> $unpaidInvoices,
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'total' => 0]);
}
