<?php
header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT full_name, email, phone, company FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'user' => $user]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch profile.']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    $action = $body['action'] ?? '';

    if ($action === 'update_profile') {
        $full_name = trim($body['full_name'] ?? '');
        $phone     = trim($body['phone'] ?? '');
        $company   = trim($body['company'] ?? '');

        if (empty($full_name)) {
            echo json_encode(['success' => false, 'message' => 'Name is required.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, company = ? WHERE id = ?");
            $stmt->execute([$full_name, $phone, $company, $user_id]);
            $_SESSION['user_name'] = $full_name;
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully.', 'full_name' => $full_name]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to update profile.']);
        }
        exit;
    }

    if ($action === 'change_password') {
        $current_password = $body['current_password'] ?? '';
        $new_password     = $body['new_password'] ?? '';
        $confirm_password = $body['confirm_password'] ?? '';

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            echo json_encode(['success' => false, 'message' => 'All password fields are required.']);
            exit;
        }

        if ($new_password !== $confirm_password) {
            echo json_encode(['success' => false, 'message' => 'New passwords do not match.']);
            exit;
        }

        if (strlen($new_password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row || !password_verify($current_password, $row['password'])) {
                echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
                exit;
            }

            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $user_id]);
            echo json_encode(['success' => true, 'message' => 'Password changed successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to change password.']);
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}
