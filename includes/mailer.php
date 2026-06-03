<?php
/**
 * Shanfix Technology - Mailer
 *
 * Uses SMTP (via stream sockets) when MAIL_HOST/MAIL_USER/MAIL_PASS are set in .env.
 * Falls back to PHP mail() on unconfigured environments.
 *
 * Usage:
 *   Mailer::invoiceCreated($name, $email, $ref, $amount, $due_date);
 *   Mailer::ticketReply($name, $email, $ref, $subject, $replier);
 *   Mailer::clientReplied($admin_email, $client_name, $ref, $subject);
 *   Mailer::welcome($name, $email);
 *   Mailer::renewalReminder($name, $email, $service, $due_date, $amount);
 */

class Mailer {

    // -------------------------------------------------------------------------
    // Public template methods
    // -------------------------------------------------------------------------

    public static function welcome(string $name, string $email): bool {
        $subject = 'Welcome to Shanfix Technology Portal';
        $html = self::layout('Welcome Aboard, ' . htmlspecialchars($name) . '!', '
            <p style="color:#475569; font-size:1rem; line-height:1.7;">
                Your Shanfix client portal account has been created. You can now log in to view invoices,
                manage your services, and contact support.
            </p>
            <div style="background:#f8fafc; border-radius:12px; padding:20px; margin:24px 0; border-left:4px solid #6366f1;">
                <p style="margin:0; font-size:0.9rem; color:#64748b;"><strong>Login Email:</strong> ' . htmlspecialchars($email) . '</p>
            </div>
            <p style="color:#475569; font-size:0.9rem;">If you did not create this account, please ignore this email.</p>
        ', 'Log In to Portal', $_ENV['APP_URL'] . '/client/login.php');
        return self::send($email, $subject, $html);
    }

    public static function invoiceCreated(string $name, string $email, string $ref, float $amount, string $due_date): bool {
        $subject = "Invoice {$ref} from Shanfix Technology";
        $formatted = 'KES ' . number_format($amount, 2);
        $html = self::layout("New Invoice: {$ref}", '
            <p style="color:#475569; font-size:1rem; line-height:1.7;">
                Hi <strong>' . htmlspecialchars($name) . '</strong>, a new invoice has been issued to your account.
            </p>
            <div style="background:#f8fafc; border-radius:12px; padding:24px; margin:24px 0;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="padding:8px 0; color:#64748b; font-size:0.9rem;">Invoice Reference</td>
                        <td style="padding:8px 0; font-weight:700; text-align:right;">' . htmlspecialchars($ref) . '</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0; color:#64748b; font-size:0.9rem; border-top:1px solid #e2e8f0;">Amount Due</td>
                        <td style="padding:8px 0; font-weight:800; font-size:1.2rem; color:#6366f1; text-align:right; border-top:1px solid #e2e8f0;">' . $formatted . '</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0; color:#64748b; font-size:0.9rem; border-top:1px solid #e2e8f0;">Due Date</td>
                        <td style="padding:8px 0; font-weight:700; color:#ef4444; text-align:right; border-top:1px solid #e2e8f0;">' . htmlspecialchars($due_date) . '</td>
                    </tr>
                </table>
            </div>
            <p style="color:#475569; font-size:0.9rem;">
                <strong>Payment:</strong> M-PESA Till No. <strong>5698666</strong> or log in to your portal to download the invoice PDF.
            </p>
        ', 'View Invoice', $_ENV['APP_URL'] . '/client/index.php');
        return self::send($email, $subject, $html);
    }

    public static function ticketReply(string $name, string $email, string $ticket_ref, string $subject, string $replier_name): bool {
        $mail_subject = "Re: [{$ticket_ref}] " . $subject;
        $html = self::layout('New Reply on Your Ticket', '
            <p style="color:#475569; font-size:1rem; line-height:1.7;">
                Hi <strong>' . htmlspecialchars($name) . '</strong>,
                <strong>' . htmlspecialchars($replier_name) . '</strong> from Shanfix Support has replied to your ticket.
            </p>
            <div style="background:#f8fafc; border-radius:12px; padding:20px; margin:24px 0; border-left:4px solid #22c55e;">
                <p style="margin:0 0 6px 0; font-size:0.8rem; color:#64748b; text-transform:uppercase; font-weight:700;">Ticket</p>
                <p style="margin:0; font-weight:700; color:#1e293b;">[' . htmlspecialchars($ticket_ref) . '] ' . htmlspecialchars($subject) . '</p>
            </div>
            <p style="color:#475569; font-size:0.9rem;">Log in to your portal to read the full reply and respond.</p>
        ', 'View Ticket', $_ENV['APP_URL'] . '/client/index.php');
        return self::send($email, $mail_subject, $html);
    }

    public static function clientReplied(string $admin_email, string $client_name, string $ticket_ref, string $subject): bool {
        $mail_subject = "[{$ticket_ref}] Client replied: " . $subject;
        $html = self::layout('Client Replied to a Ticket', '
            <p style="color:#475569; font-size:1rem; line-height:1.7;">
                <strong>' . htmlspecialchars($client_name) . '</strong> has posted a new reply on their support ticket.
            </p>
            <div style="background:#f8fafc; border-radius:12px; padding:20px; margin:24px 0; border-left:4px solid #f59e0b;">
                <p style="margin:0 0 6px 0; font-size:0.8rem; color:#64748b; text-transform:uppercase; font-weight:700;">Ticket</p>
                <p style="margin:0; font-weight:700; color:#1e293b;">[' . htmlspecialchars($ticket_ref) . '] ' . htmlspecialchars($subject) . '</p>
            </div>
        ', 'Open in Admin', $_ENV['APP_URL'] . '/admin/tickets.php');
        return self::send($admin_email, $mail_subject, $html);
    }

    public static function renewalReminder(string $name, string $email, string $service_name, string $due_date, float $amount = 0): bool {
        $subject = "Service Renewal Due: {$service_name}";
        $amountLine = $amount > 0 ? '<tr>
                        <td style="padding:8px 0; color:#64748b; font-size:0.9rem; border-top:1px solid #e2e8f0;">Renewal Amount</td>
                        <td style="padding:8px 0; font-weight:800; color:#6366f1; text-align:right; border-top:1px solid #e2e8f0;">KES ' . number_format($amount, 2) . '</td>
                    </tr>' : '';
        $html = self::layout('Service Renewal Reminder', '
            <p style="color:#475569; font-size:1rem; line-height:1.7;">
                Hi <strong>' . htmlspecialchars($name) . '</strong>, your service is due for renewal soon.
                Please ensure payment is made before the due date to avoid any service interruption.
            </p>
            <div style="background:#fff7ed; border-radius:12px; padding:24px; margin:24px 0; border-left:4px solid #f59e0b;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="padding:8px 0; color:#64748b; font-size:0.9rem;">Service</td>
                        <td style="padding:8px 0; font-weight:700; text-align:right;">' . htmlspecialchars($service_name) . '</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0; color:#64748b; font-size:0.9rem; border-top:1px solid #fed7aa;">Due Date</td>
                        <td style="padding:8px 0; font-weight:800; color:#ea580c; text-align:right; border-top:1px solid #fed7aa;">' . htmlspecialchars($due_date) . '</td>
                    </tr>
                    ' . $amountLine . '
                </table>
            </div>
            <p style="color:#475569; font-size:0.9rem;">
                Pay via <strong>M-PESA Till No. 5698666</strong> or contact us for assistance.
            </p>
        ', 'Manage Services', $_ENV['APP_URL'] . '/client/index.php');
        return self::send($email, $subject, $html);
    }

    public static function contactAutoReply(string $name, string $email, string $subject): bool {
        $mail_subject = "We received your message — Shanfix Technology";
        $html = self::layout('Thanks for reaching out!', '
            <p style="color:#475569; font-size:1rem; line-height:1.7;">
                Hi <strong>' . htmlspecialchars($name) . '</strong>, we\'ve received your enquiry about
                <em>"' . htmlspecialchars($subject) . '"</em>.
            </p>
            <p style="color:#475569; font-size:1rem; line-height:1.7;">
                Our team will review your message and get back to you within <strong>24 hours</strong>.
                In the meantime, feel free to call us directly at <strong>+254 751 869 165</strong>.
            </p>
            <div style="background:#f8fafc; border-radius:12px; padding:16px; margin:24px 0; border-left:4px solid #22c55e;">
                <p style="margin:0; font-size:0.9rem; color:#64748b;">
                    <i>This is an automated confirmation. Please do not reply to this email.</i>
                </p>
            </div>
        ');
        return self::send($email, $mail_subject, $html);
    }

    public static function contactAdminNotify(string $admin_email, string $name, string $from_email, string $subject, string $message): bool {
        $mail_subject = "New Contact: {$subject}";
        $html = self::layout('New Contact Form Submission', '
            <p style="color:#475569; font-size:1rem; line-height:1.7;">
                A new enquiry has been submitted on the Shanfix website.
            </p>
            <div style="background:#f8fafc; border-radius:12px; padding:20px; margin:24px 0;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr><td style="padding:8px 0; color:#64748b; font-size:0.85rem; width:100px;">From</td><td style="padding:8px 0; font-weight:700;">' . htmlspecialchars($name) . '</td></tr>
                    <tr><td style="padding:8px 0; color:#64748b; font-size:0.85rem; border-top:1px solid #e2e8f0;">Email</td><td style="padding:8px 0; border-top:1px solid #e2e8f0;">' . htmlspecialchars($from_email) . '</td></tr>
                    <tr><td style="padding:8px 0; color:#64748b; font-size:0.85rem; border-top:1px solid #e2e8f0;">Subject</td><td style="padding:8px 0; border-top:1px solid #e2e8f0; font-weight:700;">' . htmlspecialchars($subject) . '</td></tr>
                </table>
            </div>
            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px; margin:0 0 24px;">
                <p style="margin:0 0 8px; font-size:0.75rem; text-transform:uppercase; font-weight:700; color:#64748b;">Message</p>
                <p style="margin:0; color:#1e293b; line-height:1.7;">' . nl2br(htmlspecialchars($message)) . '</p>
            </div>
        ', 'Open Admin Inbox', $_ENV['APP_URL'] . '/admin/messages.php');
        return self::send($admin_email, $mail_subject, $html);
    }

    public static function contactReply(string $name, string $email, string $original_subject, string $reply): bool {
        $mail_subject = "Re: {$original_subject} — Shanfix Technology";
        $html = self::layout('Response from Shanfix Technology', '
            <p style="color:#475569; font-size:1rem; line-height:1.7;">
                Hi <strong>' . htmlspecialchars($name) . '</strong>, our team has responded to your enquiry.
            </p>
            <div style="background:#f8fafc; border-radius:12px; padding:20px; margin:24px 0; border-left:4px solid #6366f1;">
                <p style="margin:0 0 8px; font-size:0.75rem; text-transform:uppercase; font-weight:700; color:#64748b;">Our Response</p>
                <p style="margin:0; color:#1e293b; line-height:1.7;">' . nl2br(htmlspecialchars($reply)) . '</p>
            </div>
            <p style="color:#475569; font-size:0.9rem;">
                If you have further questions, feel free to reply to this email or call <strong>+254 751 869 165</strong>.
            </p>
        ');
        return self::send($email, $mail_subject, $html);
    }

    // -------------------------------------------------------------------------
    // Core send + SMTP
    // -------------------------------------------------------------------------

    public static function send(string $to, string $subject, string $html): bool {
        $cfg = self::cfg();
        if (!empty($cfg['host']) && !empty($cfg['user']) && !empty($cfg['pass'])) {
            return self::smtp($to, $subject, $html, $cfg);
        }
        return self::native($to, $subject, $html, $cfg);
    }

    private static function cfg(): array {
        return [
            'host'      => $_ENV['MAIL_HOST']      ?? '',
            'port'      => (int)($_ENV['MAIL_PORT'] ?? 587),
            'user'      => $_ENV['MAIL_USER']      ?? '',
            'pass'      => $_ENV['MAIL_PASS']      ?? '',
            'from'      => $_ENV['MAIL_FROM']      ?? 'noreply@shanfixtechnology.com',
            'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Shanfix Technology',
        ];
    }

    private static function smtp(string $to, string $subject, string $html, array $cfg): bool {
        try {
            $port   = $cfg['port'];
            $prefix = $port === 465 ? 'ssl://' : 'tcp://';
            $sock   = @fsockopen($prefix . $cfg['host'], $port, $errno, $errstr, 15);
            if (!$sock) {
                error_log("Mailer: could not connect to {$cfg['host']}:{$port} — {$errstr}");
                return false;
            }

            $read = static function () use ($sock): string {
                $buf = '';
                while ($line = fgets($sock, 512)) {
                    $buf .= $line;
                    if (isset($line[3]) && $line[3] === ' ') break;
                }
                return $buf;
            };
            $cmd = static function (string $c) use ($sock, $read): string {
                fputs($sock, $c . "\r\n");
                return $read();
            };

            $read(); // greeting
            $cmd('EHLO ' . $cfg['host']);

            if ($port !== 465) {
                $cmd('STARTTLS');
                stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                $cmd('EHLO ' . $cfg['host']);
            }

            $cmd('AUTH LOGIN');
            $cmd(base64_encode($cfg['user']));
            $r = $cmd(base64_encode($cfg['pass']));
            if (substr(trim($r), 0, 3) !== '235') {
                error_log('Mailer: AUTH failed — ' . trim($r));
                fclose($sock);
                return false;
            }

            $cmd("MAIL FROM:<{$cfg['from']}>");
            $cmd("RCPT TO:<{$to}>");
            $cmd('DATA');

            $msg  = "From: {$cfg['from_name']} <{$cfg['from']}>\r\n";
            $msg .= "To: <{$to}>\r\n";
            $msg .= "Subject: {$subject}\r\n";
            $msg .= "MIME-Version: 1.0\r\n";
            $msg .= "Content-Type: text/html; charset=UTF-8\r\n";
            $msg .= "\r\n" . $html . "\r\n.\r\n";
            fputs($sock, $msg);

            $r = $read();
            $cmd('QUIT');
            fclose($sock);

            return substr(trim($r), 0, 3) === '250';
        } catch (Throwable $e) {
            error_log('Mailer SMTP error: ' . $e->getMessage());
            return false;
        }
    }

    private static function native(string $to, string $subject, string $html, array $cfg): bool {
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$cfg['from_name']} <{$cfg['from']}>\r\n";
        $headers .= "Reply-To: {$cfg['from']}\r\n";
        $headers .= "X-Mailer: Shanfix-PHP\r\n";
        return @mail($to, $subject, $html, $headers);
    }

    // -------------------------------------------------------------------------
    // Attachment support (MIME multipart/mixed)
    // -------------------------------------------------------------------------

    public static function sendInvoicePdf(string $name, string $email, string $ref, string $pdfBase64): bool {
        $subject = "Invoice {$ref} — Shanfix Technology";
        $html = self::layout("Your Invoice is Attached", '
            <p style="color:#475569; font-size:1rem; line-height:1.7;">
                Hi <strong>' . htmlspecialchars($name) . '</strong>, please find your invoice
                <strong>' . htmlspecialchars($ref) . '</strong> attached to this email as a PDF.
            </p>
            <div style="background:#f8fafc; border-radius:12px; padding:20px; margin:24px 0; border-left:4px solid #6366f1;">
                <p style="margin:0; font-size:0.9rem; color:#64748b;">
                    <strong>M-PESA Till No. 5698666</strong> &nbsp;&bull;&nbsp; Reference: ' . htmlspecialchars($ref) . '
                </p>
            </div>
            <p style="color:#475569; font-size:0.9rem;">
                Log in to your portal to view all your invoices and payment history.
            </p>
        ', 'Visit Your Portal', $_ENV['APP_URL'] . '/client/index.php');

        return self::sendWithAttachment($email, $subject, $html, $pdfBase64, "Invoice_{$ref}.pdf");
    }

    public static function sendWithAttachment(string $to, string $subject, string $html, string $pdfBase64, string $filename): bool {
        $cfg      = self::cfg();
        $boundary = '----=_Part_' . md5(microtime(true) . $to);

        $mime  = "--{$boundary}\r\n";
        $mime .= "Content-Type: text/html; charset=UTF-8\r\n";
        $mime .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $mime .= $html . "\r\n\r\n";
        $mime .= "--{$boundary}\r\n";
        $mime .= "Content-Type: application/pdf; name=\"{$filename}\"\r\n";
        $mime .= "Content-Transfer-Encoding: base64\r\n";
        $mime .= "Content-Disposition: attachment; filename=\"{$filename}\"\r\n\r\n";
        $mime .= chunk_split($pdfBase64, 76, "\r\n") . "\r\n";
        $mime .= "--{$boundary}--";

        if (!empty($cfg['host']) && !empty($cfg['user']) && !empty($cfg['pass'])) {
            return self::smtpAttachment($to, $subject, $mime, $boundary, $cfg);
        }
        return self::nativeAttachment($to, $subject, $mime, $boundary, $cfg);
    }

    private static function smtpAttachment(string $to, string $subject, string $mime, string $boundary, array $cfg): bool {
        try {
            $port   = $cfg['port'];
            $prefix = $port === 465 ? 'ssl://' : 'tcp://';
            $sock   = @fsockopen($prefix . $cfg['host'], $port, $errno, $errstr, 15);
            if (!$sock) return false;

            $read = static function () use ($sock): string {
                $buf = '';
                while ($line = fgets($sock, 512)) {
                    $buf .= $line;
                    if (isset($line[3]) && $line[3] === ' ') break;
                }
                return $buf;
            };
            $cmd = static function (string $c) use ($sock, $read): string {
                fputs($sock, $c . "\r\n");
                return $read();
            };

            $read();
            $cmd('EHLO ' . $cfg['host']);
            if ($port !== 465) {
                $cmd('STARTTLS');
                stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                $cmd('EHLO ' . $cfg['host']);
            }
            $cmd('AUTH LOGIN');
            $cmd(base64_encode($cfg['user']));
            $r = $cmd(base64_encode($cfg['pass']));
            if (substr(trim($r), 0, 3) !== '235') { fclose($sock); return false; }

            $cmd("MAIL FROM:<{$cfg['from']}>");
            $cmd("RCPT TO:<{$to}>");
            $cmd('DATA');

            $msg  = "From: {$cfg['from_name']} <{$cfg['from']}>\r\n";
            $msg .= "To: <{$to}>\r\n";
            $msg .= "Subject: {$subject}\r\n";
            $msg .= "MIME-Version: 1.0\r\n";
            $msg .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";
            $msg .= "\r\n" . $mime . "\r\n.\r\n";
            fputs($sock, $msg);

            $r = $read();
            $cmd('QUIT');
            fclose($sock);
            return substr(trim($r), 0, 3) === '250';
        } catch (Throwable $e) {
            error_log('Mailer SMTP attachment error: ' . $e->getMessage());
            return false;
        }
    }

    private static function nativeAttachment(string $to, string $subject, string $mime, string $boundary, array $cfg): bool {
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";
        $headers .= "From: {$cfg['from_name']} <{$cfg['from']}>\r\n";
        $headers .= "Reply-To: {$cfg['from']}\r\n";
        return @mail($to, $subject, $mime, $headers);
    }

    // -------------------------------------------------------------------------
    // HTML layout wrapper (table-based for email client compatibility)
    // -------------------------------------------------------------------------

    private static function layout(string $heading, string $body, string $btn_text = '', string $btn_url = ''): string {
        $btn = $btn_text ? '
            <div style="text-align:center; margin:32px 0 8px;">
                <a href="' . htmlspecialchars($btn_url) . '"
                   style="display:inline-block; background:#6366f1; color:#ffffff; text-decoration:none;
                          font-weight:700; font-size:0.95rem; padding:14px 36px; border-radius:50px;
                          font-family:Arial,sans-serif;">
                    ' . htmlspecialchars($btn_text) . '
                </a>
            </div>' : '';

        return '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding:40px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">

      <!-- Header -->
      <tr>
        <td style="background:#6366f1; border-radius:16px 16px 0 0; padding:28px 40px; text-align:center;">
          <span style="font-size:1.5rem; font-weight:900; color:#ffffff; letter-spacing:-0.5px;">
            Shanfix <span style="color:#a5f3fc;">Technology</span>
          </span>
        </td>
      </tr>

      <!-- Body -->
      <tr>
        <td style="background:#ffffff; padding:40px; border-radius:0 0 16px 16px; box-shadow:0 4px 24px rgba(0,0,0,0.06);">
          <h2 style="margin:0 0 20px 0; color:#1e293b; font-size:1.3rem; font-weight:800;">' . $heading . '</h2>
          ' . $body . '
          ' . $btn . '
        </td>
      </tr>

      <!-- Footer -->
      <tr>
        <td style="padding:24px 40px; text-align:center;">
          <p style="margin:0; color:#94a3b8; font-size:0.8rem;">
            &copy; ' . date('Y') . ' Shanfix Technology Limited &bull; Nairobi, Kenya<br>
            <a href="mailto:info@shanfixtechnology.com" style="color:#6366f1; text-decoration:none;">info@shanfixtechnology.com</a>
          </p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>';
    }
}
