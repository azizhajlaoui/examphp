<?php
session_start();
require_once 'db.php';
require_once 'MailHelper.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Generate 6-digit verification code
        $token = sprintf("%06d", random_int(0, 999999));
        $expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        // Delete any existing tokens for this email
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt->execute([$email]);
        
        // Insert new token
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expires_at]);
        
        // Send email using MailHelper
        $mailHelper = new MailHelper();
        if ($mailHelper->sendVerificationCode($email, $token)) {
            $_SESSION['reset_email'] = $email;
            header("Location: verify_code.php");
            exit();
        } else {
            $message = "❌ Failed to send verification code. Please try again.";
        }
    } else {
        $message = "❌ No account found with this email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Forgot Password</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($message)): ?>
                            <div class="alert <?= str_starts_with($message, '❌') ? 'alert-danger' : 'alert-success' ?>">
                                <?= $message ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Send Verification Code</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="login.php" class="btn btn-link">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 