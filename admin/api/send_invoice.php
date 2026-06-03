<?php
/**
 * API: Send Invoice PDF by Email
 * Receives a base64-encoded PDF generated client-side and mails it as an attachment.
 */

header('Content-Type: application/json');
require_once '../../includes/db_connect.php';
require_once '../../includes/mailer.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$invoice_ref     = trim($input['invoice_ref']     ?? '');
$recipient_email = trim($input['recipient_email'] ?? '');
$recipient_name  = trim($input['recipient_name']  ?? 'Client');
$pdf_base64      = $input['pdf_base64']            ?? '';

if (empty($recipient_email)) {
    echo json_encode(['success' => false, 'message' => 'No recipient email address provided.']);
    exit;
}

if (empty($pdf_base64)) {
    echo json_encode(['success' => false, 'message' => 'No PDF data received.']);
    exit;
}

if (!filter_var($recipient_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

$ok = Mailer::sendInvoicePdf($recipient_name, $recipient_email, $invoice_ref, $pdf_base64);

echo json_encode([
    'success' => $ok,
    'message' => $ok
        ? "Invoice {$invoice_ref} sent to {$recipient_email}."
        : 'Failed to send email. Check mail configuration in .env.'
]);
