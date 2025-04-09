<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord</title>
    <link rel="stylesheet" href="style.css"> <!-- si tu as un style perso -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5 text-center">
        <h2>Bienvenue, <?= htmlspecialchars($_SESSION['user_name']) ?> !</h2>
        <a href="calendar.php" class="btn btn-outline-info mt-2">Voir le calendrier</a>
        <a href="historique.php" class="btn btn-outline-secondary mt-2">Historique</a>
        <p class="mt-3">Vous êtes connecté avec succès à votre espace personnel.</p>
        <a href="logout.php" class="btn btn-danger mt-3">Se déconnecter</a>
    </div>

</body>
</html>
