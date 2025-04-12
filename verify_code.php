<?php
session_start();
require 'db.php';

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['reset_email'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];
    
    // Check if code is valid and not expired
    $stmt = $pdo->prepare("
        SELECT * FROM password_resets 
        WHERE email = ? AND token = ? AND expires_at > NOW()
    ");
    $stmt->execute([$email, $code]);
    $reset = $stmt->fetch();
    
    if ($reset) {
        $_SESSION['reset_token'] = $code;
        header("Location: reset_password.php");
        exit();
    } else {
        $message = "❌ Invalid or expired verification code.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Verify Code</title>
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
                        <h3 class="text-center">Enter Verification Code</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-danger">
                                <?= $message ?>
                            </div>
                        <?php endif; ?>
                        
                        <p class="text-center">Please enter the 6-digit code sent to your email.</p>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="code" class="form-label">Verification Code</label>
                                <input type="text" class="form-control" id="code" name="code" 
                                       pattern="[0-9]{6}" maxlength="6" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Verify Code</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="forgot_password.php" class="btn btn-link">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 