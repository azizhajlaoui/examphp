<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Suppression
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['delete'], $user_id]);
    $message = "Réservation supprimée.";
}

// Ajout
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add'])) {
    $title = $_POST['title'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $stmt = $pdo->prepare("INSERT INTO reservations (user_id, title, start_date, end_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $start, $end]);
    $message = "Réservation ajoutée.";
}

// Modification
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $stmt = $pdo->prepare("UPDATE reservations SET title=?, start_date=?, end_date=? WHERE id=? AND user_id=?");
    $stmt->execute([$title, $start, $end, $id, $user_id]);
    $message = "Réservation mise à jour.";
}

// Récupérer les réservations
$stmt = $pdo->prepare("SELECT * FROM reservations WHERE user_id = ? ORDER BY start_date");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Réservations</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Mes Réservations</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <!-- Formulaire d'ajout -->
    <form method="POST" class="mb-4">
        <h5>Nouvelle réservation</h5>
        <input type="text" name="title" placeholder="Nom de l'espace" class="form-control mb-2" required>
        <input type="date" name="start_date" class="form-control mb-2" required>
        <input type="date" name="end_date" class="form-control mb-2" required>
        <button type="submit" name="add" class="btn btn-primary">Ajouter</button>
    </form>

    <!-- Liste des réservations -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= $r['start_date'] ?></td>
                    <td><?= $r['end_date'] ?></td>
                    <td>
                        <!-- Formulaire de modification -->
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                            <input type="text" name="title" value="<?= htmlspecialchars($r['title']) ?>" class="form-control mb-1" required>
                            <input type="date" name="start_date" value="<?= $r['start_date'] ?>" class="form-control mb-1" required>
                            <input type="date" name="end_date" value="<?= $r['end_date'] ?>" class="form-control mb-1" required>
                            <button type="submit" name="edit" class="btn btn-warning btn-sm mb-1">Modifier</button>
                        </form>
                        <a href="?delete=<?= $r['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette réservation ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
