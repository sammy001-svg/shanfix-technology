<?php
/**
 * API: Service Renewal Reminders
 * Fetches active services due within 7 days and sends reminder emails.
 * Triggered manually from the admin dashboard.
 */

header('Content-Type: application/json');
require_once '../../includes/db_connect.php';
require_once '../../includes/mailer.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

try {
    $stmt = $pdo->query("
        SELECT s.id, s.service_name, s.next_due_date, s.billing_cycle,
               u.full_name, u.email,
               p.price
        FROM services s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN products p ON s.product_id = p.id
        WHERE s.status = 'active'
          AND s.next_due_date IS NOT NULL
          AND s.next_due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        ORDER BY s.next_due_date ASC
    ");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($services)) {
        echo json_encode(['success' => true, 'sent' => 0, 'message' => 'No services due within the next 7 days.']);
        exit;
    }

    $sent = 0;
    $skipped = 0;

    foreach ($services as $svc) {
        $due = date('d M Y', strtotime($svc['next_due_date']));
        $ok = Mailer::renewalReminder(
            $svc['full_name'],
            $svc['email'],
            $svc['service_name'],
            $due,
            (float)($svc['price'] ?? 0)
        );
        $ok ? $sent++ : $skipped++;
    }

    echo json_encode([
        'success' => true,
        'sent'    => $sent,
        'skipped' => $skipped,
        'message' => "Sent {$sent} renewal reminder(s). {$skipped} failed."
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
