<?php
/**
 * API: Testimonials CMS
 */
header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']); exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input  = json_decode(file_get_contents('php://input'), true) ?: [];

if ($method === 'GET') {
    $rows = $pdo->query("SELECT * FROM testimonials ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'testimonials' => $rows]);
    exit;
}

if ($method === 'DELETE') {
    $id = (int)($input['id'] ?? 0);
    $pdo->prepare("DELETE FROM testimonials WHERE id = ?")->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Testimonial deleted.']);
    exit;
}

if ($method === 'POST') {
    $action     = $input['action']     ?? 'create';
    $id         = (int)($input['id']   ?? 0);
    $quote      = trim($input['quote']      ?? '');
    $author     = trim($input['author']     ?? '');
    $company    = trim($input['company']    ?? '');
    $role       = trim($input['role']       ?? '');
    $rating     = max(1, min(5, (int)($input['rating'] ?? 5)));
    $sort_order = (int)($input['sort_order'] ?? 0);
    $is_active  = !empty($input['is_active']) ? 1 : 0;

    if (empty($quote) || empty($author)) {
        echo json_encode(['success' => false, 'message' => 'Quote and author are required.']); exit;
    }

    try {
        if ($action === 'create') {
            $pdo->prepare("INSERT INTO testimonials (quote,author,company,role,rating,sort_order,is_active) VALUES (?,?,?,?,?,?,?)")
                ->execute([$quote,$author,$company,$role,$rating,$sort_order,$is_active]);
            echo json_encode(['success' => true, 'message' => 'Testimonial added.']);
        } else {
            $pdo->prepare("UPDATE testimonials SET quote=?,author=?,company=?,role=?,rating=?,sort_order=?,is_active=? WHERE id=?")
                ->execute([$quote,$author,$company,$role,$rating,$sort_order,$is_active,$id]);
            echo json_encode(['success' => true, 'message' => 'Testimonial updated.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Save failed.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
