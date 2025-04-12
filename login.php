<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center">Connexion</h2>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success text-center">
                <?= $_SESSION['success_message'] ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <form action="login_process.php" method="POST">
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label>Mot de passe</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>
                <div class="text-center mt-3">
                    <a href="forgot_password.php" class="text-decoration-none">Mot de passe oublié ?</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
