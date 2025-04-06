<?php
require 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");

    try {
        $stmt->execute([$name, $email, $password]);
        $message = "Compte créé avec succès !";
    } catch (PDOException $e) {
        $message = "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css"> <!-- Optionnel si tu veux ajouter des styles persos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

    <div class="container mt-5">
        <h2 class="text-center">Créer un compte</h2>

        <?php if (!empty($message)) : ?>
            <div class="alert alert-info text-center mt-3">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-4">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label>Nom</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label>Mot de passe</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                </form>

                <p class="text-center mt-3">
                    Vous avez déjà un compte ? <a href="login.php">Se connecter</a>
                </p>
            </div>
        </div>
    </div>

</body>
</html>
