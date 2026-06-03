<?php
/**
 * API: Adverts & Banners CMS
 * Handles hero carousel slides (type=slide) and ad banners (type=banner).
 */

header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$type   = $_GET['type'] ?? $_POST['type'] ?? 'slide';

// -------------------------------------------------------------------------
// GET — list slides or banners
// -------------------------------------------------------------------------
if ($method === 'GET') {
    try {
        if ($type === 'banner') {
            $stmt = $pdo->query("SELECT * FROM banners ORDER BY sort_order ASC, created_at DESC");
        } else {
            $stmt = $pdo->query("SELECT * FROM adverts ORDER BY sort_order ASC, created_at DESC");
        }
        echo json_encode(['success' => true, 'items' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fetch failed.']);
    }
    exit;
}

// -------------------------------------------------------------------------
// DELETE
// -------------------------------------------------------------------------
if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id    = (int)($input['id'] ?? 0);
    $t     = $input['type'] ?? 'slide';

    try {
        $table = $t === 'banner' ? 'banners' : 'adverts';
        // Delete image file if stored locally
        if ($t === 'banner') {
            $r = $pdo->prepare("SELECT image_url FROM banners WHERE id = ?");
        } else {
            $r = $pdo->prepare("SELECT bg_image as image_url FROM adverts WHERE id = ?");
        }
        $r->execute([$id]);
        $row = $r->fetch(PDO::FETCH_ASSOC);
        if ($row && $row['image_url'] && file_exists('../../' . $row['image_url'])) {
            @unlink('../../' . $row['image_url']);
        }

        $pdo->prepare("DELETE FROM {$table} WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Deleted successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Delete failed.']);
    }
    exit;
}

// -------------------------------------------------------------------------
// POST — create or update (multipart form data for file uploads)
// -------------------------------------------------------------------------
if ($method === 'POST') {
    $action     = $_POST['action']     ?? 'create';
    $postType   = $_POST['type']       ?? 'slide';
    $id         = (int)($_POST['id']  ?? 0);
    $is_active  = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    // Helper: handle image upload
    $uploadImage = function (string $subdir): ?string {
        if (empty($_FILES['image']['name'])) return null;
        $uploadDir = "../../assets/uploads/{$subdir}/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext  = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($ext, $allowed)) return null;
        $fname = uniqid('', true) . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fname)) {
            return "assets/uploads/{$subdir}/{$fname}";
        }
        return null;
    };

    try {
        if ($postType === 'banner') {
            $title    = trim($_POST['title']    ?? '');
            $link_url = trim($_POST['link_url'] ?? '');
            $image    = $uploadImage('banners');

            if ($action === 'create') {
                if (!$image) { echo json_encode(['success' => false, 'message' => 'Banner image is required.']); exit; }
                $pdo->prepare("INSERT INTO banners (title, image_url, link_url, sort_order, is_active) VALUES (?, ?, ?, ?, ?)")
                    ->execute([$title, $image, $link_url, $sort_order, $is_active]);
                echo json_encode(['success' => true, 'message' => 'Banner added.']);
            } else {
                $existing = $_POST['existing_image'] ?? '';
                $image    = $image ?: $existing;
                $pdo->prepare("UPDATE banners SET title=?, image_url=?, link_url=?, sort_order=?, is_active=? WHERE id=?")
                    ->execute([$title, $image, $link_url, $sort_order, $is_active, $id]);
                echo json_encode(['success' => true, 'message' => 'Banner updated.']);
            }
        } else {
            // Slide
            $headline  = trim($_POST['headline']  ?? '');
            $subtitle  = trim($_POST['subtitle']  ?? '');
            $btn1_text = trim($_POST['btn1_text'] ?? 'Explore Services');
            $btn1_link = trim($_POST['btn1_link'] ?? '#services');
            $btn2_text = trim($_POST['btn2_text'] ?? '');
            $btn2_link = trim($_POST['btn2_link'] ?? '');
            $image     = $uploadImage('slides');

            if (empty($headline)) { echo json_encode(['success' => false, 'message' => 'Headline is required.']); exit; }

            if ($action === 'create') {
                $pdo->prepare("INSERT INTO adverts (headline, subtitle, btn1_text, btn1_link, btn2_text, btn2_link, bg_image, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?,?)")
                    ->execute([$headline, $subtitle, $btn1_text, $btn1_link, $btn2_text, $btn2_link, $image, $sort_order, $is_active]);
                echo json_encode(['success' => true, 'message' => 'Slide created.']);
            } else {
                $existing = $_POST['existing_image'] ?? '';
                $image    = $image ?: $existing;
                $pdo->prepare("UPDATE adverts SET headline=?, subtitle=?, btn1_text=?, btn1_link=?, btn2_text=?, btn2_link=?, bg_image=?, sort_order=?, is_active=? WHERE id=?")
                    ->execute([$headline, $subtitle, $btn1_text, $btn1_link, $btn2_text, $btn2_link, $image, $sort_order, $is_active, $id]);
                echo json_encode(['success' => true, 'message' => 'Slide updated.']);
            }
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Save failed: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
