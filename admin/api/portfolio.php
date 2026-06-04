<?php
/**
 * API: Portfolio Projects CMS
 */
header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']); exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $rows = $pdo->query("SELECT * FROM portfolio_projects ORDER BY sort_order ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'projects' => $rows]);
    exit;
}

if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id    = (int)($input['id'] ?? 0);
    // Delete image file
    $r = $pdo->prepare("SELECT image_url FROM portfolio_projects WHERE id = ?");
    $r->execute([$id]);
    $row = $r->fetch(PDO::FETCH_ASSOC);
    if ($row && $row['image_url'] && file_exists('../../' . $row['image_url'])) {
        @unlink('../../' . $row['image_url']);
    }
    $pdo->prepare("DELETE FROM portfolio_projects WHERE id = ?")->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Project deleted.']);
    exit;
}

if ($method === 'POST') {
    $action     = $_POST['action'] ?? 'create';
    $id         = (int)($_POST['id'] ?? 0);
    $title      = trim($_POST['title']       ?? '');
    $badge      = trim($_POST['badge']       ?? '');
    $desc       = trim($_POST['description'] ?? '');
    $live_url   = trim($_POST['live_url']    ?? '');
    $stat1_val  = trim($_POST['stat1_val']   ?? '');
    $stat1_lbl  = trim($_POST['stat1_label'] ?? '');
    $stat2_val  = trim($_POST['stat2_val']   ?? '');
    $stat2_lbl  = trim($_POST['stat2_label'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $is_active  = isset($_POST['is_active'])  ? 1 : 0;
    $is_featured= isset($_POST['is_featured']) ? 1 : 0;

    if (empty($title)) { echo json_encode(['success' => false, 'message' => 'Title is required.']); exit; }

    // Handle image upload
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $dir = '../../assets/uploads/portfolio/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ext  = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
            $fname = uniqid('pf_', true) . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . $fname)) {
                $image = 'assets/uploads/portfolio/' . $fname;
            }
        }
    }

    try {
        if ($action === 'create') {
            $pdo->prepare("INSERT INTO portfolio_projects (title,badge,description,image_url,live_url,stat1_val,stat1_label,stat2_val,stat2_label,sort_order,is_active,is_featured) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")
                ->execute([$title,$badge,$desc,$image,$live_url,$stat1_val,$stat1_lbl,$stat2_val,$stat2_lbl,$sort_order,$is_active,$is_featured]);
            echo json_encode(['success' => true, 'message' => 'Project added.']);
        } else {
            $existing = $_POST['existing_image'] ?? '';
            $image    = $image ?: $existing;
            $pdo->prepare("UPDATE portfolio_projects SET title=?,badge=?,description=?,image_url=?,live_url=?,stat1_val=?,stat1_label=?,stat2_val=?,stat2_label=?,sort_order=?,is_active=?,is_featured=? WHERE id=?")
                ->execute([$title,$badge,$desc,$image,$live_url,$stat1_val,$stat1_lbl,$stat2_val,$stat2_lbl,$sort_order,$is_active,$is_featured,$id]);
            echo json_encode(['success' => true, 'message' => 'Project updated.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Save failed: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
