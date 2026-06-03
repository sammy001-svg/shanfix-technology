<?php
header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT s.id, s.service_name, s.status, s.billing_cycle, s.next_due_date, s.created_at,
               p.price, p.description as product_description, c.name as category_name
        FROM services s
        LEFT JOIN products p ON s.product_id = p.id
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE s.user_id = ?
        ORDER BY s.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'services' => $services]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch services.']);
}
