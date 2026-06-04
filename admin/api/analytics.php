<?php
/**
 * API: Admin Analytics
 * Returns aggregated KPIs: client growth per month, services expiring soon.
 */
header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

try {
    // 1. Total clients
    $totalClients = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'client'")->fetchColumn();

    // 2. New clients this month
    $newThisMonth = (int) $pdo->query(
        "SELECT COUNT(*) FROM users WHERE role='client' AND MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE())"
    )->fetchColumn();

    // 3. New clients last month (for trend)
    $newLastMonth = (int) $pdo->query(
        "SELECT COUNT(*) FROM users WHERE role='client'
         AND MONTH(created_at)=MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
         AND YEAR(created_at)=YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))"
    )->fetchColumn();

    // 4. Client growth — last 6 months (month label + count)
    $growthStmt = $pdo->query("
        SELECT DATE_FORMAT(created_at, '%b') as month,
               DATE_FORMAT(created_at, '%Y-%m') as period,
               COUNT(*) as count
        FROM users
        WHERE role = 'client'
          AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY period, month
        ORDER BY period ASC
    ");
    $clientGrowth = $growthStmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Services expiring within 7 days
    $expiringStmt = $pdo->query("
        SELECT s.id, s.service_name, s.next_due_date, s.billing_cycle,
               u.full_name, u.email, p.price
        FROM services s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN products p ON s.product_id = p.id
        WHERE s.status = 'active'
          AND s.next_due_date IS NOT NULL
          AND s.next_due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        ORDER BY s.next_due_date ASC
        LIMIT 10
    ");
    $expiring = $expiringStmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. Total active services
    $activeServices = (int) $pdo->query("SELECT COUNT(*) FROM services WHERE status='active'")->fetchColumn();

    // 7. Open tickets count
    $openTickets = (int) $pdo->query("SELECT COUNT(*) FROM tickets WHERE status='open'")->fetchColumn();

    echo json_encode([
        'success'        => true,
        'total_clients'  => $totalClients,
        'new_this_month' => $newThisMonth,
        'new_last_month' => $newLastMonth,
        'client_growth'  => $clientGrowth,
        'expiring_services' => $expiring,
        'active_services' => $activeServices,
        'open_tickets'   => $openTickets,
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
