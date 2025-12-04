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

    $data = json_decode(file_get_contents('php://input'), true);
    $cart_id = intval($data['cart_id'] ?? 0);
    $quantity = intval($data['quantity'] ?? 0);
    $user_id = getCurrentUserId();

    if ($cart_id <= 0 || $quantity < 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        exit;
    }

    $conn = getDBConnection();

// Get product price
$stmt = $conn->prepare("
    SELECT p.price 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.id = ? AND c.user_id = ?
");
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Cart item not found']);
    $stmt->close();
    $conn->close();
    exit;
}

$row = $result->fetch_assoc();
$price = $row['price'];
$stmt->close();

    if ($quantity === 0) {
        // Remove item
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_id, $user_id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => true, 'subtotal' => 0]);
    } else {
        // Update quantity
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
        $stmt->execute();
        $stmt->close();
        $subtotal = $price * $quantity;
        echo json_encode(['success' => true, 'subtotal' => number_format($subtotal, 2)]);
    }

    $conn->close();
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>

