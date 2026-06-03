<?php
/**
 * Public API: Contact Form Submission
 * No authentication required — rate-limited by honeypot field.
 */

header('Content-Type: application/json');
require_once '../includes/db_connect.php';
require_once '../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

// Honeypot anti-spam: if filled in, silently accept but don't save
if (!empty($input['website'])) {
    echo json_encode(['success' => true, 'message' => 'Message received. We will get back to you shortly.']);
    exit;
}

$name    = trim($input['name']    ?? '');
$email   = trim($input['email']   ?? '');
$subject = trim($input['subject'] ?? '');
$message = trim($input['message'] ?? '');

if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

if (strlen($message) < 10) {
    echo json_encode(['success' => false, 'message' => 'Message is too short.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $subject, $message]);

    // Send auto-reply to the sender
    Mailer::contactAutoReply($name, $email, $subject);

    // Notify admin
    $adminEmail = $_ENV['ADMIN_EMAIL'] ?? '';
    if ($adminEmail) {
        Mailer::contactAdminNotify($adminEmail, $name, $email, $subject, $message);
    }

    echo json_encode(['success' => true, 'message' => 'Your message has been received. We\'ll get back to you within 24 hours.']);
} catch (PDOException $e) {
    error_log('Contact form error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Could not save your message. Please try again or email us directly.']);
}
