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
    // Validate inputs
    if (!isset($_POST['space_id']) || !isset($_POST['start_time']) || !isset($_POST['end_time'])) {
        $message = "❌ Tous les champs sont requis.";
    } else {
        $space_id = filter_var($_POST['space_id'], FILTER_VALIDATE_INT);
        $start = filter_var($_POST['start_time'], FILTER_SANITIZE_STRING);
        $end = filter_var($_POST['end_time'], FILTER_SANITIZE_STRING);

        if ($space_id === false) {
            $message = "❌ ID d'espace invalide.";
        } else if (!strtotime($start) || !strtotime($end)) {
            $message = "❌ Format de date invalide.";
        } else if (strtotime($start) >= strtotime($end)) {
            $message = "❌ La date de fin doit être après la date de début.";
        } else {
            try {
                // Vérifier si l'espace existe
                $stmt = $pdo->prepare("SELECT id FROM spaces WHERE id = ?");
                $stmt->execute([$space_id]);
                if (!$stmt->fetch()) {
                    throw new Exception("L'espace demandé n'existe pas.");
                }

                // Vérifier les conflits avec les réservations existantes
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
                $stmt->execute([$space_id, $start, $end, $start, $end, $start, $end]);
                
                if ($stmt->fetchColumn() > 0) {
                    $message = "❌ Ce créneau est déjà réservé pour cet espace.";
                } else {
                    // Ajouter la réservation
                    $stmt = $pdo->prepare("
                        INSERT INTO reservations (user_id, space_id, start_time, end_time, status)
                        VALUES (?, ?, ?, ?, 'pending')
                    ");
                    $stmt->execute([$user_id, $space_id, $start, $end]);
                    $message = "✅ Réservation effectuée avec succès !";
                }
            } catch (Exception $e) {
                error_log("Erreur de réservation: " . $e->getMessage());
                $message = "❌ Une erreur est survenue lors de la réservation.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <h2 class="mb-4">Réservation</h2>
                <?php if (!empty($message)): ?>
                    <div class="alert <?= str_starts_with($message, '✅') ? 'alert-success' : 'alert-danger' ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>
                
                <div class="mt-4">
                    <a href="calendar.php" class="btn btn-primary me-2">Voir mes réservations</a>
                    <a href="index.php" class="btn btn-outline-secondary">Retour aux espaces</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
