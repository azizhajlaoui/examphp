<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $space_id = $_POST['space_id'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];

    if ($start >= $end) {
        $message = "❌ La date de fin doit être après la date de début.";
    } else {
        // Vérifier s’il y a un conflit avec d'autres réservations (hors statut "canceled")
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM reservations
            WHERE space_id = ?
              AND status != 'canceled'
              AND (
                    (? BETWEEN start_time AND end_time)
                 OR (? BETWEEN start_time AND end_time)
                 OR (start_time BETWEEN ? AND ?)
                 OR (end_time BETWEEN ? AND ?)
              )
        ");
        $stmt->execute([
            $space_id,
            $start, $end,
            $start, $end,
            $start, $end
        ]);
        $conflictCount = $stmt->fetchColumn();

        if ($conflictCount > 0) {
            $message = "❌ Ce créneau est déjà réservé pour cet espace.";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO reservations (user_id, space_id, start_time, end_time, status)
                VALUES (?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([$user_id, $space_id, $start, $end]);
            $message = "✅ Réservation ajoutée avec succès !";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réserver</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5 text-center">
    <h2>Réservation</h2>
    <?php if (!empty($message)): ?>
        <div class="alert <?= str_starts_with($message, '✅') ? 'alert-success' : 'alert-danger' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <a href="index.php" class="btn btn-outline-primary mt-3">Retour à la liste des espaces</a>
</div>

</body>
</html>
