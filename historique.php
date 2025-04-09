<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT r.*, s.name AS space_name
    FROM reservations r
    JOIN spaces s ON r.space_id = s.id
    WHERE r.user_id = ?
    ORDER BY r.start_time DESC
");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des Réservations</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<?php include 'navbar.php'; ?>



<div class="container mt-5">
    <h2 class="text-center mb-4">Historique des Réservations</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Espace</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['space_name']) ?></td>
                    <td><?= $r['start_time'] ?></td>
                    <td><?= $r['end_time'] ?></td>
                    <td>
                        <?php if ($r['status'] === 'confirmed'): ?>
                            <span class="badge bg-success">Confirmée</span>
                        <?php elseif ($r['status'] === 'pending'): ?>
                            <span class="badge bg-warning text-dark">En attente</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Annulée</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
