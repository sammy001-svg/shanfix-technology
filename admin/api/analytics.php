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

    // 8. Revenue by month — last 12 months (paid invoices)
    $revStmt = $pdo->query("
        SELECT DATE_FORMAT(COALESCE(paid_date, issue_date, created_at), '%b') as month,
               DATE_FORMAT(COALESCE(paid_date, issue_date, created_at), '%Y-%m') as period,
               SUM(amount) as revenue,
               COUNT(*) as invoice_count
        FROM invoices
        WHERE status = 'paid'
          AND COALESCE(paid_date, issue_date, created_at) >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY period, month
        ORDER BY period ASC
    ");
    $revenueByMonth = $revStmt->fetchAll(PDO::FETCH_ASSOC);

    // 9. Total revenue (all time)
    $totalRevenue = (float) $pdo->query(
        "SELECT COALESCE(SUM(amount), 0) FROM invoices WHERE status = 'paid'"
    )->fetchColumn();

    // 10. Outstanding (unpaid) total
    $outstanding = (float) $pdo->query(
        "SELECT COALESCE(SUM(amount), 0) FROM invoices WHERE status = 'unpaid'"
    )->fetchColumn();

    // 11. This month's revenue
    $monthRevenue = (float) $pdo->query(
        "SELECT COALESCE(SUM(amount), 0) FROM invoices WHERE status = 'paid'
         AND MONTH(COALESCE(paid_date, issue_date)) = MONTH(CURDATE())
         AND YEAR(COALESCE(paid_date, issue_date))  = YEAR(CURDATE())"
    )->fetchColumn();

    // 12. Last month's revenue
    $prevMonthRevenue = (float) $pdo->query(
        "SELECT COALESCE(SUM(amount), 0) FROM invoices WHERE status = 'paid'
         AND MONTH(COALESCE(paid_date, issue_date)) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
         AND YEAR(COALESCE(paid_date, issue_date))  = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))"
    )->fetchColumn();

    // 13. Paid vs total invoices (collection rate)
    $totalInvoices = (int) $pdo->query("SELECT COUNT(*) FROM invoices")->fetchColumn();
    $paidInvoices  = (int) $pdo->query("SELECT COUNT(*) FROM invoices WHERE status = 'paid'")->fetchColumn();
    $collectionRate = $totalInvoices > 0 ? round(($paidInvoices / $totalInvoices) * 100) : 0;

    // 14. Top services by revenue
    $topServicesStmt = $pdo->query("
        SELECT s.service_name, COUNT(*) as count, COALESCE(SUM(i.amount), 0) as revenue
        FROM services s
        LEFT JOIN invoices i ON i.user_id = s.user_id AND i.status = 'paid'
        WHERE s.status = 'active'
        GROUP BY s.service_name
        ORDER BY revenue DESC
        LIMIT 5
    ");
    $topServices = $topServicesStmt->fetchAll(PDO::FETCH_ASSOC);

    // 15. Pending orders count
    $pendingOrders = (int) $pdo->query(
        "SELECT COUNT(*) FROM orders WHERE status NOT IN ('delivered','cancelled')"
    )->fetchColumn();

    echo json_encode([
        'success'           => true,
        'total_clients'     => $totalClients,
        'new_this_month'    => $newThisMonth,
        'new_last_month'    => $newLastMonth,
        'client_growth'     => $clientGrowth,
        'expiring_services' => $expiring,
        'active_services'   => $activeServices,
        'open_tickets'      => $openTickets,
        'revenue_by_month'  => $revenueByMonth,
        'total_revenue'     => $totalRevenue,
        'outstanding'       => $outstanding,
        'month_revenue'     => $monthRevenue,
        'prev_month_revenue'=> $prevMonthRevenue,
        'collection_rate'   => $collectionRate,
        'total_invoices'    => $totalInvoices,
        'paid_invoices'     => $paidInvoices,
        'top_services'      => $topServices,
        'pending_orders'    => $pendingOrders,
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
