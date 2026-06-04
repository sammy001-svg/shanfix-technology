<?php
/**
 * API: Client Forgot Password
 * Generates a time-limited reset token and emails it to the client.
 */
header('Content-Type: application/json');
require_once '../../includes/db_connect.php';
require_once '../../includes/mailer.php';

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$email = trim($input['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'A valid email address is required.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE email = ? AND role = 'client' AND status = 'active'");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Always respond the same way — don't leak account existence
    if (!$user) {
        echo json_encode(['success' => true, 'message' => 'If that email is registered, a reset link has been sent.']);
        exit;
    }

    // Invalidate previous tokens
    $pdo->prepare("UPDATE password_resets SET used=1 WHERE email=? AND role='client'")->execute([$email]);

    $token   = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

    $pdo->prepare("INSERT INTO password_resets (email, token, role, expires_at) VALUES (?, ?, 'client', ?)")
        ->execute([$email, $token, $expires]);

    $resetUrl = ($_ENV['APP_URL'] ?? '') . '/client/reset.php?token=' . $token;
    Mailer::passwordReset($user['full_name'], $email, $resetUrl, 'client');

    echo json_encode(['success' => true, 'message' => 'If that email is registered, a reset link has been sent.']);
} catch (PDOException $e) {
    error_log('Forgot password error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error. Please try again.']);
}
