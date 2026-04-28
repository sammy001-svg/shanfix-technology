<?php
/**
 * API: Client Login
 * Shanfix Technology - Secure Authentication
 */

header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

session_start();

// Get POST data (handle both JSON and standard form-data)
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    // Fallback to standard $_POST if JSON decode fails
    $input = $_POST;
}

if (empty($input)) {
    echo json_encode(['success' => false, 'message' => 'No data received by the server.']);
    exit;
}

$email = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit;
}

try {
    // 1. Fetch user by email
    $stmt = $pdo->prepare("SELECT id, full_name, email, password, role, status FROM users WHERE email = ? AND role = 'client'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // 2. Check if account is active
        if ($user['status'] !== 'active') {
            echo json_encode(['success' => false, 'message' => 'Your account is currently ' . $user['status'] . '. Please contact support.']);
            exit;
        }

        // 3. Verify password
        if (password_verify($password, $user['password'])) {
            // Success! Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['is_client'] = true;

            echo json_encode([
                'success' => true, 
                'message' => 'Login successful!',
                'user' => [
                    'name' => $user['full_name'],
                    'email' => $user['email']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No partner account found with this email.']);
    }

} catch (PDOException $e) {
    error_log("Login Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred. Please contact support.']);
}
