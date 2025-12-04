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
        echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
        exit;
    }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$product_id = intval($_POST['product_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 1);
$user_id = getCurrentUserId();

if ($product_id <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
    exit;
}

$conn = getDBConnection();

// Check if product exists and get stock
$stmt = $conn->prepare("SELECT id, stock FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    $stmt->close();
    $conn->close();
    exit;
}

$product = $result->fetch_assoc();
$stmt->close();

// Check stock availability
$stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$cartItem = $stmt->get_result()->fetch_assoc();
$stmt->close();

$newQuantity = $quantity;
if ($cartItem) {
    $newQuantity = $cartItem['quantity'] + $quantity;
}

if ($newQuantity > $product['stock']) {
    echo json_encode(['success' => false, 'message' => 'Insufficient stock available']);
    $conn->close();
    exit;
}

// Add or update cart
if ($cartItem) {
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $newQuantity, $cartItem['id']);
} else {
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);
}

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product added to cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>

