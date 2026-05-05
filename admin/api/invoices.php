<?php
/**
 * API: Advanced Invoice Management
 */
header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT i.*, u.full_name as reg_client_name, u.email as reg_client_email 
                             FROM invoices i 
                             LEFT JOIN users u ON i.user_id = u.id 
                             ORDER BY i.created_at DESC");
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fetch items for each invoice
        foreach ($invoices as &$inv) {
            $itemStmt = $pdo->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
            $itemStmt->execute([$inv['id']]);
            $inv['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        echo json_encode(['success' => true, 'invoices' => $invoices]);
    } 
    elseif ($method === 'POST') {
        $action = $input['action'] ?? 'create';
        
        if ($action === 'create') {
            $user_id = !empty($input['user_id']) ? $input['user_id'] : null;
            $guest_name = $input['guest_name'] ?? null;
            $guest_email = $input['guest_email'] ?? null;
            $guest_phone = $input['guest_phone'] ?? null;
            
            $subtotal = $input['subtotal'] ?? 0;
            $tax_amount = $input['tax_amount'] ?? 0;
            $amount = $input['total_amount'] ?? 0;
            $terms = $input['terms'] ?? '70% Prior, 30% Upon Delivery';
            $due_date = $input['due_date'] ?? date('Y-m-d', strtotime('+7 days'));
            $items = $input['items'] ?? [];

            if (!$user_id && empty($guest_name)) {
                throw new Exception('Please select a client or enter guest details.');
            }

            $pdo->beginTransaction();

            $ref = 'INV-' . date('ymd') . '-' . rand(100, 999);
            
            $stmt = $pdo->prepare("INSERT INTO invoices (user_id, guest_name, guest_email, guest_phone, reference, amount, subtotal, tax_amount, status, issue_date, due_date, terms_payment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'unpaid', CURDATE(), ?, ?)");
            $stmt->execute([$user_id, $guest_name, $guest_email, $guest_phone, $ref, $amount, $subtotal, $tax_amount, $due_date, $terms]);
            $invoice_id = $pdo->lastInsertId();

            foreach ($items as $item) {
                $itemStmt = $pdo->prepare("INSERT INTO invoice_items (invoice_id, description, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)");
                $itemStmt->execute([$invoice_id, $item['desc'], $item['qty'], $item['price'], ($item['qty'] * $item['price'])]);
            }

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Invoice generated successfully.', 'id' => $invoice_id, 'reference' => $ref]);
        }
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
