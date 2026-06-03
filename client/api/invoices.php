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

try {
    // Single invoice detail (for PDF generation)
    if (isset($_GET['ref'])) {
        $ref = $_GET['ref'];
        $stmt = $pdo->prepare("
            SELECT i.*, u.full_name as client_name, u.email as client_email, u.phone as client_phone, u.company as client_company
            FROM invoices i
            LEFT JOIN users u ON i.user_id = u.id
            WHERE i.reference = ? AND i.user_id = ?
        ");
        $stmt->execute([$ref, $user_id]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            echo json_encode(['success' => false, 'message' => 'Invoice not found.']);
            exit;
        }

        $stmt2 = $pdo->prepare("SELECT description, quantity, unit_price, total_price FROM invoice_items WHERE invoice_id = ?");
        $stmt2->execute([$invoice['id']]);
        $invoice['items'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'invoice' => $invoice]);
        exit;
    }

    // Invoice list
    $stmt = $pdo->prepare("
        SELECT reference as id, issue_date as date, due_date, amount as total, status
        FROM invoices WHERE user_id = ? ORDER BY issue_date DESC
    ");
    $stmt->execute([$user_id]);
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'invoices' => $invoices]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch invoices.']);
}
