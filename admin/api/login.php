<?php
/**
 * API: Admin Login
 * Shanfix Technology - Secure Authentication
 */

header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit;
}

ob_start();

try {
    $stmt = $pdo->prepare("SELECT id, full_name, password, role FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Clear any existing session
        session_unset();
        
        // Set secure session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['role'] = 'admin';
        
        ob_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Authentication successful.',
            'admin_name' => $user['full_name']
        ]);
    } else {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid administrator credentials.']);
    }
} catch (PDOException $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
}
