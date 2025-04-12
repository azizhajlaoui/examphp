<?php
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'email_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailHelper {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        
        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host = SMTP_HOST;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = SMTP_USERNAME;
        $this->mailer->Password = SMTP_PASSWORD;
        $this->mailer->SMTPSecure = SMTP_SECURE;
        $this->mailer->Port = SMTP_PORT;
        
        // Sender
        $this->mailer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    }

    public function sendVerificationCode($to, $code) {
        try {
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Password Reset Verification Code';
            
            $message = "
                <html>
                <head>
                    <title>Password Reset Verification Code</title>
                </head>
                <body>
                    <h2>Password Reset Request</h2>
                    <p>Your verification code is: <strong>{$code}</strong></p>
                    <p>This code will expire in 15 minutes.</p>
                    <p>If you didn't request this, please ignore this email.</p>
                </body>
                </html>
            ";
            
            $this->mailer->Body = $message;
            $this->mailer->AltBody = "Your verification code is: {$code}\nThis code will expire in 15 minutes.";
            
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Mail Error: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
}
?> 