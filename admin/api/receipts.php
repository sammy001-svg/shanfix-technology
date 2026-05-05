<?php
/**
 * API: Advanced Receipt Management
 */
header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // Updated to handle both registered and guest clients
        $stmt = $pdo->query("
            SELECT r.*, 
                   i.reference as invoice_ref, 
                   COALESCE(u.full_name, i.guest_name) as client_name,
                   COALESCE(u.email, i.guest_email) as client_email,
                   i.amount as total_amount,
                   i.subtotal,
                   i.tax_amount
            FROM receipts r 
            JOIN invoices i ON r.invoice_id = i.id 
            LEFT JOIN users u ON i.user_id = u.id 
            ORDER BY r.created_at DESC
        ");
        $receipts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fetch itemized lines for the linked invoice for each receipt
        foreach ($receipts as &$rec) {
            $itemStmt = $pdo->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
            $itemStmt->execute([$rec['invoice_id']]);
            $rec['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        echo json_encode(['success' => true, 'receipts' => $receipts]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
