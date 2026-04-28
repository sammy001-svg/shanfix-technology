<?php
/**
 * API: Client Invoices
 * Shanfix Technology - Database Retrieval
 */

header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$user_id = $_SESSION['user_id'];
ob_start();

try {
    $stmt = $pdo->prepare("SELECT reference as id, issue_date as date, amount as total, status FROM invoices WHERE user_id = ? ORDER BY issue_date DESC");
    $stmt->execute([$user_id]);
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ob_clean();
    echo json_encode(['success' => true, 'invoices' => $invoices]);
} catch (PDOException $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Failed to fetch invoices.']);
}
