<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Validate input
$space_id = null;
if (isset($_POST['space_id'])) {
    $space_id = filter_var($_POST['space_id'], FILTER_VALIDATE_INT);
    if ($space_id === false) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid space ID']);
        exit();
    }
}

try {
    $sql = "
        SELECT r.space_id, r.start_time, r.end_time, r.status, s.name AS space_name
        FROM reservations r
        JOIN spaces s ON r.space_id = s.id
        WHERE r.status != 'canceled'
    ";

    $params = [];
    if ($space_id !== null) {
        $sql .= " AND r.space_id = :space_id";
        $params[':space_id'] = $space_id;
    }

    $stmt = $pdo->prepare($sql);
    if (!$stmt->execute($params)) {
        throw new PDOException("Failed to execute query");
    }

    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $events = array_map(function($r) {
        return [
            'title' => htmlspecialchars($r['space_name']) . ' (' . htmlspecialchars($r['status']) . ')',
            'start' => $r['start_time'],
            'end' => $r['end_time'],
            'color' => '#dc3545',
            'spaceId' => (int)$r['space_id']
        ];
    }, $reservations);

    echo json_encode($events);

} catch (PDOException $e) {
    error_log("Database error in get_reservations.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
    exit();
} 