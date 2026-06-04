<?php
/**
 * API: Blog / News CMS
 */
header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']); exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// ── GET ───────────────────────────────────────────────────────────────────
if ($method === 'GET') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($post ? ['success' => true, 'post' => $post] : ['success' => false, 'message' => 'Not found.']);
    } else {
        $rows = $pdo->query("SELECT id, title, slug, excerpt, category, status, author_name, views, published_at, created_at, featured_image FROM blog_posts ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'posts' => $rows]);
    }
    exit;
}

// ── DELETE ────────────────────────────────────────────────────────────────
if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id    = (int)($input['id'] ?? 0);
    $r = $pdo->prepare("SELECT featured_image FROM blog_posts WHERE id = ?");
    $r->execute([$id]);
    $row = $r->fetch(PDO::FETCH_ASSOC);
    if ($row && $row['featured_image'] && file_exists('../../' . $row['featured_image'])) {
        @unlink('../../' . $row['featured_image']);
    }
    $pdo->prepare("DELETE FROM blog_posts WHERE id = ?")->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Post deleted.']);
    exit;
}

// ── POST (create / update) ────────────────────────────────────────────────
if ($method === 'POST') {
    $action  = $_POST['action']  ?? 'create';
    $id      = (int)($_POST['id'] ?? 0);
    $title   = trim($_POST['title']    ?? '');
    $content = trim($_POST['content']  ?? '');
    $excerpt = trim($_POST['excerpt']  ?? '');
    $category= trim($_POST['category'] ?? 'News');
    $status  = in_array($_POST['status'] ?? '', ['draft','published']) ? $_POST['status'] : 'draft';
    $author  = $_SESSION['admin_name'] ?? $_SESSION['user_name'] ?? 'Shanfix Team';

    if (empty($title) || empty($content)) {
        echo json_encode(['success' => false, 'message' => 'Title and content are required.']); exit;
    }

    // Generate slug from title
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    // Ensure unique slug
    $slugBase = $slug;
    $i = 1;
    while (true) {
        $chk = $pdo->prepare("SELECT id FROM blog_posts WHERE slug = ? AND id != ?");
        $chk->execute([$slug, $id]);
        if (!$chk->fetch()) break;
        $slug = $slugBase . '-' . $i++;
    }

    $publishedAt = ($status === 'published') ? date('Y-m-d H:i:s') : null;

    // Handle featured image upload
    $image = null;
    if (!empty($_FILES['featured_image']['name'])) {
        $dir = '../../assets/uploads/blog/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
            $fname = 'post_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $dir . $fname)) {
                $image = 'assets/uploads/blog/' . $fname;
            }
        }
    }

    try {
        if ($action === 'create') {
            $pdo->prepare("INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, category, author_id, author_name, status, published_at) VALUES (?,?,?,?,?,?,?,?,?,?)")
                ->execute([$title, $slug, $excerpt, $content, $image, $category, $_SESSION['user_id'], $author, $status, $publishedAt]);
            echo json_encode(['success' => true, 'message' => 'Post ' . ($status === 'published' ? 'published' : 'saved as draft') . '.']);
        } else {
            $existing = $_POST['existing_image'] ?? '';
            $image    = $image ?: $existing;
            // Only update published_at if publishing for first time
            $pubStmt  = $pdo->prepare("SELECT published_at FROM blog_posts WHERE id = ?");
            $pubStmt->execute([$id]);
            $existingPub = $pubStmt->fetchColumn();
            if ($status === 'published' && !$existingPub) {
                $publishedAt = date('Y-m-d H:i:s');
            } elseif ($status === 'published') {
                $publishedAt = $existingPub;
            }
            $pdo->prepare("UPDATE blog_posts SET title=?, slug=?, excerpt=?, content=?, featured_image=?, category=?, status=?, published_at=?, updated_at=NOW() WHERE id=?")
                ->execute([$title, $slug, $excerpt, $content, $image, $category, $status, $publishedAt, $id]);
            echo json_encode(['success' => true, 'message' => 'Post updated.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Save failed: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
