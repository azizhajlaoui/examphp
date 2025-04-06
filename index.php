<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservations Coworking</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<!-- 
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Coworking Réservations</a>
            <a href="login.php" class="btn btn-primary">Connexion</a>
        </div>
    </nav> -->
    <?php include 'navbar.php'; ?>


    <div class="container mt-4">
        <h2 class="text-center">Espaces Disponibles</h2>
        <div class="row">
            <?php
            // Connexion à la base de données (PDO)
            require 'db.php';  
            $query = $pdo->query("SELECT * FROM spaces");
            while ($space = $query->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($space['name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($space['description']) ?></p>
                            <p><strong>Capacité:</strong> <?= $space['capacity'] ?> personnes</p>
                            <p><strong>Prix:</strong> <?= $space['price_per_hour'] ?>€/h</p>
                            <a href="reserve.php?space_id=<?= $space['id'] ?>" class="btn btn-success">Réserver</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>
