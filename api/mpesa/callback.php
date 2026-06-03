<?php
/**
 * M-PESA Daraja Callback
 * Called by Safaricom servers when a payment completes or fails.
 * MUST be publicly accessible via HTTPS.
 * No session/auth — validated by matching CheckoutRequestID.
 */

require_once '../../includes/db_connect.php';

// Log every callback for debugging
$raw = file_get_contents('php://input');
error_log('M-PESA Callback received: ' . $raw);

$input    = json_decode($raw, true);
$callback = $input['Body']['stkCallback'] ?? null;

if (!$callback) {
    http_response_code(200);
    echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    exit;
}

$checkoutRequestId = $callback['CheckoutRequestID'] ?? '';
$resultCode        = (int)($callback['ResultCode']  ?? -1);
$resultDesc        = $callback['ResultDesc']         ?? '';

try {
    $stmt = $pdo->prepare("SELECT id, invoice_id, amount FROM mpesa_transactions WHERE checkout_request_id = ?");
    $stmt->execute([$checkoutRequestId]);
    $txn = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('M-PESA Callback DB error: ' . $e->getMessage());
    http_response_code(200);
    echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    exit;
}

if (!$txn) {
    error_log("M-PESA Callback: no transaction found for CheckoutRequestID {$checkoutRequestId}");
    http_response_code(200);
    echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    exit;
}

try {
    if ($resultCode === 0) {
        // ── Payment successful ────────────────────────────────────────────
        $meta    = $callback['CallbackMetadata']['Item'] ?? [];
        $receipt = '';
        $amount  = $txn['amount'];

        foreach ($meta as $item) {
            match ($item['Name'] ?? '') {
                'MpesaReceiptNumber' => ($receipt = $item['Value'] ?? ''),
                'Amount'             => ($amount  = $item['Value'] ?? $txn['amount']),
                default              => null
            };
        }

        // Update transaction
        $pdo->prepare(
            "UPDATE mpesa_transactions SET status='completed', mpesa_receipt=?, result_code=?, result_desc=? WHERE id=?"
        )->execute([$receipt, $resultCode, $resultDesc, $txn['id']]);

        // Mark invoice paid
        $pdo->prepare(
            "UPDATE invoices SET status='paid', paid_date=NOW() WHERE id=? AND status='unpaid'"
        )->execute([$txn['invoice_id']]);

        // Create receipt record (uses existing receipts table)
        $receiptRef = 'RCP-' . strtoupper(substr(md5($receipt . time()), 0, 8));
        $pdo->prepare(
            "INSERT IGNORE INTO receipts (invoice_id, receipt_ref, amount_paid, payment_method, transaction_ref)
             VALUES (?, ?, ?, 'M-PESA', ?)"
        )->execute([$txn['invoice_id'], $receiptRef, $amount, $receipt]);

        error_log("M-PESA: Payment confirmed — invoice #{$txn['invoice_id']}, receipt {$receipt}");
    } else {
        // ── Payment failed / cancelled ────────────────────────────────────
        $pdo->prepare(
            "UPDATE mpesa_transactions SET status='failed', result_code=?, result_desc=? WHERE id=?"
        )->execute([$resultCode, $resultDesc, $txn['id']]);

        error_log("M-PESA: Payment failed — txn #{$txn['id']}, code {$resultCode}: {$resultDesc}");
    }
} catch (PDOException $e) {
    error_log('M-PESA Callback processing error: ' . $e->getMessage());
}

http_response_code(200);
echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
