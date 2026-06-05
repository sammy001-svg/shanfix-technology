<?php
header('Content-Type: application/json');
require_once '../../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']); exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input  = json_decode(file_get_contents('php://input'), true) ?: [];

// Auto-create tables
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `events` (`id` int(11) NOT NULL AUTO_INCREMENT,`title` varchar(200) NOT NULL,`slug` varchar(220) NOT NULL,`description` text DEFAULT NULL,`event_date` datetime NOT NULL,`end_date` datetime DEFAULT NULL,`venue` varchar(200) DEFAULT NULL,`venue_address` varchar(300) DEFAULT NULL,`image_url` varchar(255) DEFAULT NULL,`organizer` varchar(100) DEFAULT 'Shanfix Technology',`status` enum('draft','published','cancelled','completed') DEFAULT 'published',`is_featured` tinyint(1) DEFAULT 0,`created_at` timestamp DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), UNIQUE KEY `slug` (`slug`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $pdo->exec("CREATE TABLE IF NOT EXISTS `event_ticket_types` (`id` int(11) NOT NULL AUTO_INCREMENT,`event_id` int(11) NOT NULL,`name` varchar(100) NOT NULL,`description` varchar(255) DEFAULT NULL,`price` decimal(10,2) NOT NULL DEFAULT 0,`capacity` int(11) DEFAULT NULL,`sold_count` int(11) NOT NULL DEFAULT 0,`sale_ends` datetime DEFAULT NULL,`status` enum('active','paused','sold_out') DEFAULT 'active', PRIMARY KEY (`id`), KEY `event_id` (`event_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $pdo->exec("CREATE TABLE IF NOT EXISTS `event_bookings` (`id` int(11) NOT NULL AUTO_INCREMENT,`reference` varchar(20) NOT NULL,`event_id` int(11) NOT NULL,`buyer_name` varchar(100) NOT NULL,`buyer_email` varchar(100) NOT NULL,`buyer_phone` varchar(25) NOT NULL,`total_amount` decimal(10,2) NOT NULL,`payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',`mpesa_receipt` varchar(50) DEFAULT NULL,`created_at` timestamp DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), UNIQUE KEY `reference` (`reference`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Exception $e) {}

if ($method === 'GET') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id) {
        $ev = $pdo->prepare("SELECT * FROM events WHERE id=?"); $ev->execute([$id]);
        $event = $ev->fetch(PDO::FETCH_ASSOC);
        if (!$event) { echo json_encode(['success' => false, 'message' => 'Not found.']); exit; }
        $tt = $pdo->prepare("SELECT * FROM event_ticket_types WHERE event_id=? ORDER BY price ASC"); $tt->execute([$id]);
        $event['ticket_types'] = $tt->fetchAll(PDO::FETCH_ASSOC);
        $bk = $pdo->prepare("SELECT COUNT(*) as count, SUM(total_amount) as revenue FROM event_bookings WHERE event_id=? AND payment_status='paid'"); $bk->execute([$id]);
        $event['booking_stats'] = $bk->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'event' => $event]); exit;
    }
    // List all events
    $events = $pdo->query("SELECT e.*, COUNT(eb.id) as booking_count, COALESCE(SUM(CASE WHEN eb.payment_status='paid' THEN eb.total_amount ELSE 0 END),0) as revenue FROM events e LEFT JOIN event_bookings eb ON eb.event_id=e.id GROUP BY e.id ORDER BY e.event_date DESC")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'events' => $events]); exit;
}

if ($method === 'DELETE') {
    $id = (int)($input['id'] ?? 0);
    $pdo->prepare("DELETE FROM events WHERE id=?")->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Event deleted.']); exit;
}

if ($method === 'POST') {
    $action      = $input['action']      ?? 'create';
    $id          = (int)($input['id']    ?? 0);
    $title       = trim($input['title']  ?? '');
    $description = trim($input['description'] ?? '');
    $eventDate   = $input['event_date']  ?? '';
    $endDate     = $input['end_date']    ?? null;
    $venue       = trim($input['venue']  ?? '');
    $venueAddr   = trim($input['venue_address'] ?? '');
    $organizer   = trim($input['organizer'] ?? 'Shanfix Technology');
    $imageUrl    = trim($input['image_url'] ?? '');
    $status      = $input['status']      ?? 'published';
    $isFeatured  = !empty($input['is_featured']) ? 1 : 0;
    $ticketTypes = $input['ticket_types'] ?? [];

    if (empty($title) || empty($eventDate)) {
        echo json_encode(['success' => false, 'message' => 'Title and event date are required.']); exit;
    }

    // Generate slug
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $title)) . '-' . date('Y');

    try {
        if ($action === 'create') {
            $pdo->prepare("INSERT INTO events (title,slug,description,event_date,end_date,venue,venue_address,organizer,image_url,status,is_featured) VALUES (?,?,?,?,?,?,?,?,?,?,?)")
                ->execute([$title, $slug, $description, $eventDate, $endDate ?: null, $venue, $venueAddr, $organizer, $imageUrl, $status, $isFeatured]);
            $id = (int)$pdo->lastInsertId();
        } else {
            $pdo->prepare("UPDATE events SET title=?,description=?,event_date=?,end_date=?,venue=?,venue_address=?,organizer=?,image_url=?,status=?,is_featured=? WHERE id=?")
                ->execute([$title, $description, $eventDate, $endDate ?: null, $venue, $venueAddr, $organizer, $imageUrl, $status, $isFeatured, $id]);
            // Remove old ticket types and re-insert
            $pdo->prepare("DELETE FROM event_ticket_types WHERE event_id=? AND sold_count=0")->execute([$id]);
        }

        // Save ticket types
        foreach ($ticketTypes as $tt) {
            $ttName  = trim($tt['name'] ?? '');
            $ttPrice = (float)($tt['price'] ?? 0);
            $ttCap   = isset($tt['capacity']) && $tt['capacity'] !== '' ? (int)$tt['capacity'] : null;
            $ttDesc  = trim($tt['description'] ?? '');
            if (!$ttName) continue;
            if (!empty($tt['id'])) {
                $pdo->prepare("UPDATE event_ticket_types SET name=?,description=?,price=?,capacity=? WHERE id=? AND event_id=?")
                    ->execute([$ttName, $ttDesc, $ttPrice, $ttCap, (int)$tt['id'], $id]);
            } else {
                $pdo->prepare("INSERT INTO event_ticket_types (event_id,name,description,price,capacity) VALUES (?,?,?,?,?)")
                    ->execute([$id, $ttName, $ttDesc, $ttPrice, $ttCap]);
            }
        }
        echo json_encode(['success' => true, 'message' => $action === 'create' ? 'Event created.' : 'Event updated.', 'id' => $id]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Save failed: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
