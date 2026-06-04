<?php
/**
 * API: Client Reset Password
 * Validates the token and updates the user's password.
 */
header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

$input    = json_decode(file_get_contents('php://input'), true) ?: [];
$token    = trim($input['token']    ?? '');
$password = trim($input['password'] ?? '');
$confirm  = trim($input['confirm']  ?? '');

if (empty($token) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Token and new password are required.']);
    exit;
}
if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']);
    exit;
}
if ($password !== $confirm) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND role = 'client' AND used = 0 AND expires_at > NOW()");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reset) {
        echo json_encode(['success' => false, 'message' => 'This reset link is invalid or has expired.']);
        exit;
    }

    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $pdo->prepare("UPDATE users SET password = ? WHERE email = ? AND role = 'client'")->execute([$hashed, $reset['email']]);
    $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?")->execute([$reset['id']]);

    echo json_encode(['success' => true, 'message' => 'Password updated successfully. You can now log in.']);
} catch (PDOException $e) {
    error_log('Reset password error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error. Please try again.']);
}
