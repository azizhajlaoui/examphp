<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user reservations
$stmt = $pdo->prepare("
    SELECT r.id, r.start_time, r.end_time, r.status, s.name AS space_name
    FROM reservations r
    JOIN spaces s ON r.space_id = s.id
    WHERE r.user_id = ?
");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon calendrier</title>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">

    <!-- jQuery (necessary for FullCalendar 3.x or older, if you're using jQuery) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap CSS (optional, for styling) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        #calendar {
            max-width: 900px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h2 class="text-center">Mes réservations - Vue Calendrier</h2>
    <div id="calendar"></div>
</div>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            events: [
                <?php foreach ($reservations as $index => $r): ?>,
                    {
                        title: "<?= htmlspecialchars($r['space_name']) ?> (<?= $r['status'] ?>)",
                        start: "<?= $r['start_time'] ?>",
                        end: "<?= $r['end_time'] ?>"
                    },
                    <?php if ($index < count($reservations) - 1) { echo ','; } ?>,
                <?php endforeach; ?>
            ]
        });
        calendar.render();
    });
</script>

</body>
</html>
