<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $space_id = $_POST['space_id'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $message = "";

    if ($start >= $end) {
        $message = "❌ La date de fin doit être après la date de début.";
    } else {
        // Vérifier les conflits avec les réservations existantes
        $stmt = $pdo->prepare("
            SELECT * FROM reservations
            WHERE space_id = ?
              AND status != 'canceled'
              AND (
                    (? BETWEEN start_time AND end_time) OR
                    (? BETWEEN start_time AND end_time) OR
                    (start_time BETWEEN ? AND ?) OR
                    (end_time BETWEEN ? AND ?)
                  )
        ");
        $stmt->execute([
            $space_id,
            $start, $end,
            $start, $end,
            $start, $end
        ]);

        if ($stmt->rowCount() > 0) {
            $message = "❌ Ce créneau est déjà réservé pour cet espace.";
        } else {
            // Ajouter la réservation
            $stmt = $pdo->prepare("INSERT INTO reservations (user_id, space_id, start_time, end_time, status)
                                   VALUES (?, ?, ?, ?, 'pending')");
            $stmt->execute([$user_id, $space_id, $start, $end]);
            $message = "✅ Réservation effectuée avec succès !";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réservation</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5 text-center">
    <h2>Réservation</h2>
    <div class="alert <?= str_starts_with($message, '✅') ? 'alert-success' : 'alert-danger' ?>">
        <?= $message ?>
    </div>
    <a href="index.php" class="btn btn-outline-primary mt-3">Retour à la liste des espaces</a>
</div>

</body>
</html>
