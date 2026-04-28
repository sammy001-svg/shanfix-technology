<?php
/**
 * API: Client Registration
 * Shanfix Technology - Secure Database Integration
 */

header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

// Clean any accidental output (like warnings or whitespace) to ensure valid JSON
ob_start();

// Get POST data (handle both JSON and standard form-data)
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    // Fallback to standard $_POST if JSON decode fails
    $input = $_POST;
}

if (empty($input)) {
    error_log("Registration Attempt Failed: No input data received.");
    echo json_encode(['success' => false, 'message' => 'No data received by the server.']);
    exit;
}

$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');

if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

try {
    // 1. Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'An account with this email already exists.']);
        exit;
    }

    // 2. Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // 3. Insert new user (role defaults to 'client' as per schema)
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role, status) VALUES (?, ?, ?, 'client', 'active')");
    $result = $stmt->execute([$name, $email, $hashedPassword]);

    if ($result) {
        ob_clean();
        echo json_encode(['success' => true, 'message' => 'Portal account created successfully!']);
    } else {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Failed to create account. Please try again.']);
    }

} catch (PDOException $e) {
    error_log("Registration Error: " . $e->getMessage());
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Database error occurred. Please contact support.']);
}
