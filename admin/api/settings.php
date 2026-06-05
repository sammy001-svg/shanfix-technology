<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once '../../includes/db_connect.php';
header('Content-Type: application/json');

// Ensure settings table exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `settings` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `setting_key` varchar(100) NOT NULL,
        `setting_value` text DEFAULT NULL,
        `setting_group` varchar(50) NOT NULL DEFAULT 'general',
        `is_sensitive` tinyint(1) NOT NULL DEFAULT 0,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `setting_key` (`setting_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Exception $e) {}

$method = $_SERVER['REQUEST_METHOD'];

// GET /admin/api/settings.php?group=email
if ($method === 'GET') {
    $group = trim($_GET['group'] ?? '');
    try {
        if ($group) {
            $stmt = $pdo->prepare("SELECT setting_key, setting_value, setting_group, is_sensitive FROM settings WHERE setting_group = ?");
            $stmt->execute([$group]);
        } else {
            $stmt = $pdo->query("SELECT setting_key, setting_value, setting_group, is_sensitive FROM settings ORDER BY setting_group, setting_key");
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $out  = [];
        foreach ($rows as $r) {
            // Mask sensitive values
            $out[$r['setting_key']] = [
                'value'     => (int)$r['is_sensitive'] ? (empty($r['setting_value']) ? '' : '••••••••') : $r['setting_value'],
                'group'     => $r['setting_group'],
                'sensitive' => (bool)$r['is_sensitive'],
            ];
        }
        echo json_encode(['success' => true, 'settings' => $out]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// POST — save a group of settings
if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    if (!is_array($body) || empty($body['settings'])) {
        echo json_encode(['success' => false, 'error' => 'No settings provided']);
        exit;
    }
    $group    = $body['group'] ?? 'general';
    $settings = $body['settings'];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO settings (setting_key, setting_value, setting_group)
            VALUES (:k, :v, :g)
            ON DUPLICATE KEY UPDATE
                setting_value = IF(is_sensitive = 1 AND :v2 = '••••••••', setting_value, :v3),
                setting_group = :g2
        ");
        foreach ($settings as $key => $value) {
            $stmt->execute([
                ':k'  => $key,
                ':v'  => $value,
                ':g'  => $group,
                ':v2' => $value,
                ':v3' => $value,
                ':g2' => $group,
            ]);
        }
        echo json_encode(['success' => true, 'message' => 'Settings saved.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
