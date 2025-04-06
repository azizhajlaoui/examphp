<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver un espace</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

    <div class="container mt-5">
        <h2 class="text-center">Réserver un espace</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="reserve_process.php" method="POST">
                    <input type="hidden" name="space_id" value="<?= $_GET['space_id'] ?>">
                    
                    <div class="mb-3">
                        <label>Date et heure de début</label>
                        <input type="datetime-local" class="form-control" name="start_time" required>
                    </div>

                    <div class="mb-3">
                        <label>Date et heure de fin</label>
                        <input type="datetime-local" class="form-control" name="end_time" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Réserver</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
