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
        echo json_encode(['success' => false, 'message' => 'Please login']);
        exit;
    }

    $cart_id = intval($_GET['id'] ?? 0);
    $user_id = getCurrentUserId();

    if ($cart_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid cart item']);
        exit;
    }

    $conn = getDBConnection();

    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>

