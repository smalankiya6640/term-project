<?php
// Suppress all output except JSON
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

require_once '../config/database.php';

// Clear any output
ob_clean();
header('Content-Type: application/json');

try {
    $query = trim($_GET['q'] ?? '');

    if (strlen($query) < 2) {
        echo json_encode([]);
        exit;
    }

    $conn = getDBConnection();

    $searchTerm = "%$query%";
    $stmt = $conn->prepare("SELECT id, name, price, image_url FROM products WHERE name LIKE ? OR description LIKE ? LIMIT 5");
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode($products);

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    ob_clean();
    echo json_encode([]);
}
?>

