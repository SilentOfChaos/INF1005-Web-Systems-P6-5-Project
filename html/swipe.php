<?php
// /api/swipe.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

require_once '/var/www/config/db.php';

$data      = json_decode(file_get_contents('php://input'), true);
$swipedId  = isset($data['swiped_id'])  ? (int)$data['swiped_id']       : 0;
$direction = isset($data['direction'])  ? $data['direction']             : '';
$swiperId  = $_SESSION['user_id'];

if (!$swipedId || !in_array($direction, ['like', 'pass'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit();
}

try {
    // Record the swipe (ignore duplicate if already swiped)
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO swipes (swiper_id, swiped_id, direction)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$swiperId, $swipedId, $direction]);

    // Check for a match — did they already like us back?
    $match = false;
    if ($direction === 'like') {
        $check = $pdo->prepare("
            SELECT id FROM swipes
            WHERE swiper_id = ? AND swiped_id = ? AND direction = 'like'
        ");
        $check->execute([$swipedId, $swiperId]);
        $match = (bool) $check->fetch();
    }

    echo json_encode(['success' => true, 'match' => $match]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}