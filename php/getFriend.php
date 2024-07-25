<?php
session_start();
require 'config.php';

if (!isset($_SESSION['id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['id'];
$postData = json_decode(file_get_contents('php://input'), true);
$lastUpdated = isset($postData['lastUpdated']) ? $postData['lastUpdated'] : null;

try {
    $query = "
        SELECT u.id, u.username, f.lastmsg, f.status, f.user_id, f.friend_id, u.avatar, f.time 
        FROM friends f 
        JOIN users u ON (f.user_id = u.id OR f.friend_id = u.id)
        WHERE (f.user_id = ? OR f.friend_id = ?)
        AND f.status = 'active'
        AND u.id != ?
    ";

    $params = [$user_id, $user_id, $user_id];
    if ($lastUpdated) {
        $query .= " AND f.time > ?";
        $params[] = $lastUpdated;
    }

    $query .= " ORDER BY f.time DESC";

    $stmt = $conn->prepare($query);

    // Generate type string dynamically
    $types = str_repeat('i', count($params)); // 'i' for integer placeholder

    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $friends = $result->fetch_all(MYSQLI_ASSOC);

    $lastUpdatedQuery = "SELECT MAX(f.time) as lastUpdated FROM friends f WHERE (f.user_id = ? OR f.friend_id = ?) AND f.status = 'active'";
    $stmt = $conn->prepare($lastUpdatedQuery);
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $lastUpdatedResult = $stmt->get_result();
    $lastUpdatedRow = $lastUpdatedResult->fetch_assoc();
    $lastUpdatedTimestamp = $lastUpdatedRow['lastUpdated'];

    echo json_encode(['status' => 'success', 'friends' => $friends, 'lastUpdated' => $lastUpdatedTimestamp]);
    exit();
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error', 'msg' => $e->getMessage()]);
    exit();
}
?>
