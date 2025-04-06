<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - Coworking</title>
    <link rel="stylesheet" href="style.css"> <!-- facultatif -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5 text-center">
    <h1>Bienvenue sur notre plateforme de Coworking</h1>
    <p class="lead mt-3">Gérez vos réservations d'espaces de travail en toute simplicité.</p>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="login.php" class="btn btn-primary mt-4 me-2">Se connecter</a>
        <a href="register.php" class="btn btn-outline-primary mt-4">Créer un compte</a>
    <?php else: ?>
        <a href="dashboard.php" class="btn btn-success mt-4">Aller à mon espace</a>
    <?php endif; ?>
</div>

</body>
</html>
