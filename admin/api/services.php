<?php
/**
 * API: Client Subscription Management
 * CRUD for the services table (active client subscriptions).
 * Also generates renewal invoices on demand.
 */

header('Content-Type: application/json');
require_once '../../includes/db_connect.php';
require_once '../../includes/mailer.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input  = json_decode(file_get_contents('php://input'), true);

// ── GET: list all subscriptions ───────────────────────────────────────────
if ($method === 'GET') {
    try {
        $stmt = $pdo->query("
            SELECT s.id, s.service_name, s.status, s.billing_cycle, s.next_due_date, s.created_at,
                   u.id as user_id, u.full_name as client_name, u.email as client_email,
                   p.id as product_id, p.name as product_name, p.price as product_price
            FROM services s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN products p ON s.product_id = p.id
            ORDER BY
                CASE WHEN s.next_due_date IS NOT NULL AND s.next_due_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                     AND s.status = 'active' THEN 0 ELSE 1 END,
                s.next_due_date ASC,
                s.created_at DESC
        ");
        echo json_encode(['success' => true, 'services' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Fetch failed.']);
    }
    exit;
}

// ── DELETE ────────────────────────────────────────────────────────────────
if ($method === 'DELETE') {
    $id = (int)($input['id'] ?? 0);
    try {
        $pdo->prepare("DELETE FROM services WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Subscription removed.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Delete failed.']);
    }
    exit;
}

// ── POST ──────────────────────────────────────────────────────────────────
if ($method === 'POST') {
    $action = $input['action'] ?? '';

    // ── Create subscription ───────────────────────────────────────────────
    if ($action === 'create') {
        $user_id      = (int)($input['user_id']      ?? 0);
        $product_id   = !empty($input['product_id']) ? (int)$input['product_id'] : null;
        $service_name = trim($input['service_name']  ?? '');
        $billing_cycle = $input['billing_cycle']     ?? 'monthly';
        $next_due_date = $input['next_due_date']      ?? null;
        $status        = $input['status']             ?? 'active';

        if (!$user_id || empty($service_name)) {
            echo json_encode(['success' => false, 'message' => 'Client and service name are required.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO services (user_id, product_id, service_name, status, billing_cycle, next_due_date)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$user_id, $product_id, $service_name, $status, $billing_cycle, $next_due_date ?: null]);
            echo json_encode(['success' => true, 'message' => 'Subscription created.', 'id' => (int)$pdo->lastInsertId()]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Create failed.']);
        }
        exit;
    }

    // ── Update subscription ───────────────────────────────────────────────
    if ($action === 'update') {
        $id            = (int)($input['id']            ?? 0);
        $service_name  = trim($input['service_name']   ?? '');
        $billing_cycle = $input['billing_cycle']        ?? 'monthly';
        $next_due_date = $input['next_due_date']         ?? null;
        $status        = $input['status']               ?? 'active';

        if (!$id) { echo json_encode(['success' => false, 'message' => 'ID required.']); exit; }

        try {
            // Fetch previous status to detect a status change
            $prevStmt = $pdo->prepare("SELECT s.status, s.service_name, u.full_name, u.email
                                       FROM services s JOIN users u ON s.user_id = u.id WHERE s.id = ?");
            $prevStmt->execute([$id]);
            $prev = $prevStmt->fetch(PDO::FETCH_ASSOC);

            $pdo->prepare(
                "UPDATE services SET service_name=?, billing_cycle=?, next_due_date=?, status=? WHERE id=?"
            )->execute([$service_name, $billing_cycle, $next_due_date ?: null, $status, $id]);

            // Notify client if status changed
            if ($prev && $prev['status'] !== $status) {
                Mailer::serviceStatusChanged(
                    $prev['full_name'],
                    $prev['email'],
                    $service_name ?: $prev['service_name'],
                    $status
                );
            }

            echo json_encode(['success' => true, 'message' => 'Subscription updated.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Update failed.']);
        }
        exit;
    }

    // ── Generate renewal invoice for one subscription ─────────────────────
    if ($action === 'generate_invoice') {
        $id = (int)($input['id'] ?? 0);
        if (!$id) { echo json_encode(['success' => false, 'message' => 'Service ID required.']); exit; }

        try {
            $stmt = $pdo->prepare("
                SELECT s.*, u.id as user_id, p.price, p.name as prod_name
                FROM services s
                JOIN users u ON s.user_id = u.id
                LEFT JOIN products p ON s.product_id = p.id
                WHERE s.id = ?
            ");
            $stmt->execute([$id]);
            $svc = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$svc) { echo json_encode(['success' => false, 'message' => 'Subscription not found.']); exit; }

            $price = (float)($svc['price'] ?? 0);
            if ($price <= 0) {
                echo json_encode(['success' => false, 'message' => 'No price set for this service. Link it to a catalog product first.']);
                exit;
            }

            $ref     = 'INV-' . date('ymd') . '-' . rand(100, 999);
            $dueDate = date('Y-m-d', strtotime('+7 days'));
            $vat     = round($price * 0.16, 2);
            $total   = $price + $vat;

            $pdo->beginTransaction();

            $pdo->prepare(
                "INSERT INTO invoices (user_id, reference, amount, subtotal, tax_amount, status, issue_date, due_date, terms_payment)
                 VALUES (?, ?, ?, ?, ?, 'unpaid', CURDATE(), ?, 'Renewal Payment')"
            )->execute([$svc['user_id'], $ref, $total, $price, $vat, $dueDate]);
            $invId = (int)$pdo->lastInsertId();

            $pdo->prepare(
                "INSERT INTO invoice_items (invoice_id, description, quantity, unit_price, total_price)
                 VALUES (?, ?, 1, ?, ?)"
            )->execute([$invId, "Renewal: {$svc['service_name']}", $price, $price]);

            // Advance next_due_date based on billing cycle
            if ($svc['billing_cycle'] === 'monthly') {
                $newDue = date('Y-m-d', strtotime(($svc['next_due_date'] ?? 'now') . ' +1 month'));
            } elseif ($svc['billing_cycle'] === 'yearly') {
                $newDue = date('Y-m-d', strtotime(($svc['next_due_date'] ?? 'now') . ' +1 year'));
            } else {
                $newDue = null;
            }
            if ($newDue) {
                $pdo->prepare("UPDATE services SET next_due_date = ? WHERE id = ?")->execute([$newDue, $id]);
            }

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => "Invoice {$ref} generated.", 'reference' => $ref]);
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Invoice generation failed.']);
        }
        exit;
    }

    // ── Batch: generate invoices for all services due within 7 days ───────
    if ($action === 'generate_due') {
        try {
            $stmt = $pdo->query("
                SELECT s.id, s.service_name, s.billing_cycle, s.next_due_date,
                       u.id as user_id, p.price
                FROM services s
                JOIN users u ON s.user_id = u.id
                LEFT JOIN products p ON s.product_id = p.id
                WHERE s.status = 'active'
                  AND s.next_due_date IS NOT NULL
                  AND s.next_due_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                  AND (p.price IS NULL OR p.price > 0)
            ");
            $due = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($due)) {
                echo json_encode(['success' => true, 'generated' => 0, 'message' => 'No services due within 7 days.']);
                exit;
            }

            $generated = 0;
            foreach ($due as $svc) {
                if ((float)($svc['price'] ?? 0) <= 0) continue;

                $ref   = 'INV-' . date('ymd') . '-' . rand(100, 999);
                $price = (float)$svc['price'];
                $vat   = round($price * 0.16, 2);
                $total = $price + $vat;
                $dueD  = date('Y-m-d', strtotime('+7 days'));

                try {
                    $pdo->beginTransaction();
                    $pdo->prepare(
                        "INSERT INTO invoices (user_id, reference, amount, subtotal, tax_amount, status, issue_date, due_date, terms_payment)
                         VALUES (?, ?, ?, ?, ?, 'unpaid', CURDATE(), ?, 'Renewal Payment')"
                    )->execute([$svc['user_id'], $ref, $total, $price, $vat, $dueD]);
                    $invId = (int)$pdo->lastInsertId();

                    $pdo->prepare(
                        "INSERT INTO invoice_items (invoice_id, description, quantity, unit_price, total_price) VALUES (?, ?, 1, ?, ?)"
                    )->execute([$invId, "Renewal: {$svc['service_name']}", $price, $price]);

                    if ($svc['billing_cycle'] === 'monthly') {
                        $newDue = date('Y-m-d', strtotime($svc['next_due_date'] . ' +1 month'));
                        $pdo->prepare("UPDATE services SET next_due_date = ? WHERE id = ?")->execute([$newDue, $svc['id']]);
                    } elseif ($svc['billing_cycle'] === 'yearly') {
                        $newDue = date('Y-m-d', strtotime($svc['next_due_date'] . ' +1 year'));
                        $pdo->prepare("UPDATE services SET next_due_date = ? WHERE id = ?")->execute([$newDue, $svc['id']]);
                    }

                    $pdo->commit();
                    $generated++;
                } catch (PDOException $e) {
                    if ($pdo->inTransaction()) $pdo->rollBack();
                }
            }

            echo json_encode([
                'success'   => true,
                'generated' => $generated,
                'message'   => "Generated {$generated} renewal invoice(s)."
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Batch generation failed.']);
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}
