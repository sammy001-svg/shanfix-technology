<?php
/**
 * API: Client Onboarding
 * One-shot endpoint: assign service + generate invoice + send welcome email.
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

$clientId    = (int)($input['client_id']       ?? 0);
$clientName  = trim($input['client_name']       ?? '');
$clientEmail = trim($input['client_email']      ?? '');

if (!$clientId) {
    echo json_encode(['success' => false, 'message' => 'Client ID is required.']);
    exit;
}

$serviceId   = null;
$invoiceRef  = null;
$emailSent   = false;
$messages    = [];

try {
    // ── 1. Assign service ─────────────────────────────────────────────────
    if (!empty($input['assign_service'])) {
        $serviceName  = trim($input['service_name']  ?? '');
        $productId    = !empty($input['product_id']) ? (int)$input['product_id'] : null;
        $billingCycle = $input['billing_cycle']       ?? 'monthly';
        $nextDue      = !empty($input['next_due_date']) ? $input['next_due_date'] : null;

        if (empty($serviceName)) {
            $messages[] = 'Service not assigned: service name is required.';
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO services (user_id, product_id, service_name, status, billing_cycle, next_due_date)
                 VALUES (?, ?, ?, 'active', ?, ?)"
            );
            $stmt->execute([$clientId, $productId, $serviceName, $billingCycle, $nextDue]);
            $serviceId  = (int)$pdo->lastInsertId();
            $messages[] = "Service \"{$serviceName}\" assigned.";
        }
    }

    // ── 2. Generate invoice ───────────────────────────────────────────────
    if (!empty($input['generate_invoice'])) {
        $amount  = (float)($input['invoice_amount'] ?? 0);
        $desc    = trim($input['invoice_desc'] ?? ($serviceId ? 'Initial service setup' : 'Service setup'));

        if ($amount <= 0) {
            $messages[] = 'Invoice not generated: amount must be greater than 0.';
        } else {
            $vat   = round($amount * 0.16, 2);
            $total = $amount + $vat;
            $ref   = 'INV-' . date('ymd') . '-' . rand(100, 999);
            $due   = date('Y-m-d', strtotime('+7 days'));

            $pdo->beginTransaction();
            $pdo->prepare(
                "INSERT INTO invoices (user_id, reference, amount, subtotal, tax_amount, status, issue_date, due_date, terms_payment)
                 VALUES (?, ?, ?, ?, ?, 'unpaid', CURDATE(), ?, 'Full Payment')"
            )->execute([$clientId, $ref, $total, $amount, $vat, $due]);
            $invId = (int)$pdo->lastInsertId();

            $pdo->prepare(
                "INSERT INTO invoice_items (invoice_id, description, quantity, unit_price, total_price)
                 VALUES (?, ?, 1, ?, ?)"
            )->execute([$invId, $desc, $amount, $amount]);

            $pdo->commit();
            $invoiceRef = $ref;
            $messages[] = "Invoice {$ref} generated (KES " . number_format($total, 2) . " incl. VAT).";
        }
    }

    // ── 3. Send welcome email ─────────────────────────────────────────────
    if (!empty($input['send_email']) && $clientEmail) {
        $emailSent = Mailer::welcome($clientName, $clientEmail);
        $messages[] = $emailSent ? 'Welcome email sent.' : 'Welcome email failed — check mail config.';
    }

    echo json_encode([
        'success'     => true,
        'service_id'  => $serviceId,
        'invoice_ref' => $invoiceRef,
        'email_sent'  => $emailSent,
        'message'     => implode(' ', $messages) ?: 'Onboarding completed.'
    ]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('Onboard error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A database error occurred during onboarding.']);
}
