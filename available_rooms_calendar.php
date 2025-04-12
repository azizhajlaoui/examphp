<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // Fetch all spaces
    $stmt = $pdo->query("SELECT id, name, capacity FROM spaces");
    if (!$stmt) {
        throw new PDOException("Error fetching spaces");
    }
    $spaces = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all reservations
    $stmt = $pdo->query("
        SELECT r.space_id, r.start_time, r.end_time, r.status, s.name AS space_name
        FROM reservations r
        JOIN spaces s ON r.space_id = s.id
        WHERE r.status != 'canceled'
    ");
    if (!$stmt) {
        throw new PDOException("Error fetching reservations");
    }
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
    <title>Calendrier des salles disponibles</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        #calendar {
            max-width: 1200px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }
        .fc-event {
            cursor: pointer;
        }
        .room-info {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h2 class="text-center">Calendrier des salles disponibles</h2>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php else: ?>
            <div class="row room-info">
                <?php foreach ($spaces as $space): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($space['name']) ?></h5>
                                <p class="card-text">Capacité: <?= htmlspecialchars((string)$space['capacity']) ?> personnes</p>
                                <button class="btn btn-primary btn-sm" onclick="filterCalendar(<?= (int)$space['id'] ?>)">
                                    Voir cette salle
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div id="calendar"></div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
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
                events: [
                    <?php 
                    if (isset($reservations)) {
                        foreach ($reservations as $index => $r) {
                            echo json_encode([
                                'title' => htmlspecialchars($r['space_name']) . ' (' . htmlspecialchars($r['status']) . ')',
                                'start' => $r['start_time'],
                                'end' => $r['end_time'],
                                'color' => '#dc3545',
                                'spaceId' => (int)$r['space_id']
                            ]);
                            if ($index < count($reservations) - 1) {
                                echo ',';
                            }
                        }
                    }
                    ?>
                ],
                eventClick: function(info) {
                    // Show reservation details
                    alert('Salle: ' + info.event.title + '\n' +
                          'Début: ' + info.event.start.toLocaleString() + '\n' +
                          'Fin: ' + (info.event.end ? info.event.end.toLocaleString() : 'Non spécifié'));
                },
                dateClick: function(info) {
                    // Handle date click for new reservation
                    window.location.href = 'reserve.php?date=' + encodeURIComponent(info.dateStr);
                }
            });
            calendar.render();

            // Function to filter calendar by room
            window.filterCalendar = function(spaceId) {
                if (!spaceId || typeof spaceId !== 'number') {
                    console.error('Invalid space ID');
                    return;
                }
                calendar.removeAllEvents();
                calendar.addEventSource({
                    url: 'get_reservations.php',
                    method: 'POST',
                    extraParams: {
                        space_id: spaceId
                    },
                    failure: function(error) {
                        console.error('Error loading events:', error);
                        alert('Erreur lors du chargement des réservations');
                    }
                });
            };
        });
    </script>
</body>
</html> 