<?php
/**
 * API: Client Management
 */
header('Content-Type: application/json');
require_once '../../includes/db_connect.php';
require_once '../../includes/mailer.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    if ($method === 'GET') {
        // pending_count=1 is used by the sidebar badge
        if (!empty($_GET['pending_count'])) {
            $cnt = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='client' AND status='inactive'")->fetchColumn();
            echo json_encode(['success' => true, 'count' => $cnt]);
            exit;
        }
        $stmt = $pdo->query("SELECT id, full_name, email, phone, company, status, created_at FROM users WHERE role = 'client' ORDER BY FIELD(status,'inactive','active','suspended') ASC, created_at DESC");
        $clients = $stmt->fetchAll();
        $pendingCount = count(array_filter($clients, fn($c) => $c['status'] === 'inactive'));
        echo json_encode(['success' => true, 'clients' => $clients, 'pending_count' => $pendingCount]);
    } 
    elseif ($method === 'POST') {
        $action = $input['action'] ?? 'create';
        $full_name = trim($input['full_name'] ?? '');
        $email = trim($input['email'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $company = trim($input['company'] ?? '');
        $status = $input['status'] ?? 'active';
        $id = $input['id'] ?? null;

        if ($action === 'create') {
            if (empty($full_name) || empty($email)) {
                throw new Exception('Name and Email are required.');
            }
            
            // Use provided password or default
            $raw_password = !empty($input['password']) ? $input['password'] : 'Client@123';
            $password = password_hash($raw_password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role, phone, company, status) VALUES (?, ?, ?, 'client', ?, ?, ?)");
            $stmt->execute([$full_name, $email, $password, $phone, $company, $status]);
            $newClientId = (int)$pdo->lastInsertId();
            echo json_encode(['success' => true, 'message' => 'Client registered successfully. Password: ' . $raw_password, 'client_id' => $newClientId]);
        } 
        elseif ($action === 'update') {
            if (!$id) throw new Exception('Client ID is missing.');
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, company = ?, status = ? WHERE id = ? AND role = 'client'");
            $stmt->execute([$full_name, $email, $phone, $company, $status, $id]);
            echo json_encode(['success' => true, 'message' => 'Client updated successfully.']);
        }
        elseif ($action === 'approve') {
            if (!$id) throw new Exception('Client ID is missing.');
            $uStmt = $pdo->prepare("SELECT full_name, email FROM users WHERE id = ? AND role = 'client'");
            $uStmt->execute([$id]);
            $u = $uStmt->fetch();
            $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ? AND role = 'client'")->execute([$id]);
            if ($u) Mailer::clientApproved($u['full_name'], $u['email']);
            echo json_encode(['success' => true, 'message' => 'Client approved and notified.']);
        }
        elseif ($action === 'reject') {
            if (!$id) throw new Exception('Client ID is missing.');
            $reason = trim($input['reason'] ?? '');
            $uStmt  = $pdo->prepare("SELECT full_name, email FROM users WHERE id = ? AND role = 'client'");
            $uStmt->execute([$id]);
            $u = $uStmt->fetch();
            $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'client'")->execute([$id]);
            if ($u) Mailer::clientRejected($u['full_name'], $u['email'], $reason);
            echo json_encode(['success' => true, 'message' => 'Client rejected and removed.']);
        }
        elseif ($action === 'reset_password') {
            if (!$id) throw new Exception('Client ID is missing.');
            $new_password = $input['new_password'] ?? 'Client@123';
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ? AND role = 'client'");
            $stmt->execute([$hashed, $id]);
            echo json_encode(['success' => true, 'message' => 'Password reset successfully to: ' . $new_password]);
        }
    } 
    elseif ($method === 'DELETE') {
        $id = $input['id'] ?? null;
        if (!$id) throw new Exception('Client ID is missing.');
        
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'client'");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Client deleted successfully.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
