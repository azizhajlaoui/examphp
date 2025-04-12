<?php
session_start();
require 'db.php';

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_token'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['reset_email'];
$token = $_SESSION['reset_token'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $message = "❌ Passwords do not match.";
    } else {
        // Verify token again
        $stmt = $pdo->prepare("
            SELECT * FROM password_resets 
            WHERE email = ? AND token = ? AND expires_at > NOW()
        ");
        $stmt->execute([$email, $token]);
        $reset = $stmt->fetch();
        
        if ($reset) {
            // Update password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$hashed_password, $email]);
            
            // Delete used token
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt->execute([$email]);
            
            // Clear session
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_token']);
            
            $_SESSION['success_message'] = "✅ Password has been reset successfully. Please login with your new password.";
            header("Location: login.php");
            exit();
        } else {
            $message = "❌ Invalid or expired verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
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
                        <h3 class="text-center">Reset Password</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-danger">
                                <?= $message ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Reset Password</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="verify_code.php" class="btn btn-link">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 