<?php
// Simple mail helper. Uses PHPMailer if installed via Composer, otherwise falls back to mail().
// Reads SMTP settings from environment variables (see .env.example).

function send_mail_simple(string $to, string $subject, string $body, string $from = null): bool {
    // Try PHPMailer first
    $composerAutoload = __DIR__ . '/../vendor/autoload.php';
    if (is_readable($composerAutoload)) {
        require_once $composerAutoload;
        if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $useSmtp = getenv('SMTP_HOST') ?: null;
                if ($useSmtp) {
                    $mail->isSMTP();
                    $mail->Host = getenv('SMTP_HOST');
                    $mail->Port = getenv('SMTP_PORT') ?: 25;
                    $mail->SMTPAuth = getenv('SMTP_USER') ? true : false;
                    if ($mail->SMTPAuth) {
                        $mail->Username = getenv('SMTP_USER');
                        $mail->Password = getenv('SMTP_PASS');
                    }
                    $smtpSecure = getenv('SMTP_SECURE');
                    if ($smtpSecure) {
                        $mail->SMTPSecure = $smtpSecure;
                    }
                }

                $fromAddr = $from ?: (getenv('MAIL_FROM') ?: ('no-reply@' . ($_SERVER['SERVER_NAME'] ?? 'localhost')));
                if (strpos($fromAddr, '<') !== false) {
                    // allow "Name <email@host>"
                    $mail->setFrom($fromAddr);
                } else {
                    $mail->setFrom($fromAddr);
                }

                $mail->addAddress($to);
                $mail->Subject = $subject;
                $mail->Body = $body;
                $mail->AltBody = strip_tags($body);
                $mail->isHTML(false);
                return $mail->send();
            } catch (Exception $e) {
                error_log('PHPMailer error: ' . $e->getMessage());
                // fall through to mail() fallback
            }
        }
    }

    // Fallback to PHP mail()
    $headers = [];
    $fromAddr = $from ?: (getenv('MAIL_FROM') ?: ('no-reply@' . ($_SERVER['SERVER_NAME'] ?? 'localhost')));
    $headers[] = 'From: ' . $fromAddr;
    $headers[] = 'Content-Type: text/plain; charset=utf-8';
    $headersStr = implode("\r\n", $headers);
    try {
        return mail($to, $subject, $body, $headersStr);
    } catch (Exception $e) {
        error_log('mail() error: ' . $e->getMessage());
        return false;
    }
}

?>
