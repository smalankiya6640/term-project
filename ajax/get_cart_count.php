<?php
// Suppress all output except JSON
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

// Clear any output
ob_clean();
header('Content-Type: application/json');

try {
    if (!isLoggedIn()) {
        echo json_encode(['count' => 0]);
        exit;
    }

    $conn = getDBConnection();
    $user_id = getCurrentUserId();

    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    echo json_encode(['count' => intval($row['total'] ?? 0)]);

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['count' => 0, 'error' => 'Database error']);
}
?>

