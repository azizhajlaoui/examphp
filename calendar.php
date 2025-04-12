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

try {
    // Fetch user reservations
    $stmt = $pdo->prepare("
        SELECT r.id, r.start_time, r.end_time, r.status, s.name AS space_name
        FROM reservations r
        JOIN spaces s ON r.space_id = s.id
        WHERE r.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error_message = "Une erreur est survenue lors de la récupération des données.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon calendrier</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FullCalendar Bundle -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/fr.global.min.js'></script>

    <style>
        #calendar {
            max-width: 900px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .fc-event {
            cursor: pointer;
        }
        body {
            padding-top: 60px;
        }
        .fc-toolbar-title {
            text-transform: capitalize;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h2 class="text-center mb-4">Mes réservations - Vue Calendrier</h2>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <div id="calendar"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: "Aujourd'hui",
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour'
        },
        events: [
            <?php 
            if (!empty($reservations)) {
                foreach ($reservations as $index => $r) {
                    $color = '';
                    switch ($r['status']) {
                        case 'confirmed':
                            $color = '#28a745';
                            break;
                        case 'pending':
                            $color = '#ffc107';
                            break;
                        case 'canceled':
                            $color = '#dc3545';
                            break;
                        default:
                            $color = '#6c757d';
                    }
                    echo json_encode([
                        'title' => $r['space_name'] . ' (' . $r['status'] . ')',
                        'start' => $r['start_time'],
                        'end' => $r['end_time'],
                        'color' => $color,
                        'textColor' => ($r['status'] === 'pending' ? '#000' : '#fff')
                    ]);
                    if ($index < count($reservations) - 1) {
                        echo ",\n";
                    }
                }
            }
            ?>
        ],
        eventClick: function(info) {
            alert(
                'Détails de la réservation:\n' +
                'Espace: ' + info.event.title + '\n' +
                'Début: ' + info.event.start.toLocaleString('fr-FR') + '\n' +
                'Fin: ' + (info.event.end ? info.event.end.toLocaleString('fr-FR') : 'Non spécifié')
            );
        },
        dayMaxEvents: true,
        firstDay: 1, // Start week on Monday
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
        allDaySlot: false,
        height: 'auto',
        timeZone: 'local'
    });
    calendar.render();
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
