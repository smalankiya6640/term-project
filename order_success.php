<?php
$pageTitle = 'Order Success - Online Computer Store';
require_once 'config/database.php';
require_once 'config/auth.php';
requireLogin();
require_once 'includes/header.php';

$order_id = $_GET['order_id'] ?? 0;

if (!$order_id) {
    header('Location: orders.php');
    exit();
}

$conn = getDBConnection();
$user_id = getCurrentUserId();

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    header('Location: orders.php');
    exit();
}
?>

<div class="text-center fade-in">
    <div class="alert alert-success p-5" style="border-radius: 1rem;">
        <i class="bi bi-check-circle" style="font-size: 5rem; color: var(--success-color);"></i>
        <h2 class="mt-4 mb-3" style="font-weight: 800;">Order Placed Successfully!</h2>
        <p class="lead mb-0">Thank you for your purchase. We'll process your order shortly.</p>
    </div>
    
    <div class="card mx-auto shadow-lg" style="max-width: 500px; border-radius: 1rem;">
        <div class="card-body p-4">
            <h5 class="mb-4" style="font-weight: 700; color: var(--primary-color);"><i class="bi bi-receipt-cutoff"></i> Order Details</h5>
            <div class="text-start">
                <p class="mb-3"><strong><i class="bi bi-hash"></i> Order ID:</strong> <span class="text-primary">#<?php echo $order['id']; ?></span></p>
                <p class="mb-3"><strong><i class="bi bi-currency-dollar"></i> Total Amount:</strong> <span class="text-success" style="font-size: 1.25rem; font-weight: 700;">$<?php echo number_format($order['total_price'], 2); ?></span></p>
                <p class="mb-3"><strong><i class="bi bi-calendar"></i> Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['order_date'])); ?></p>
                <p class="mb-0"><strong><i class="bi bi-info-circle"></i> Status:</strong> <span class="badge bg-info" style="font-size: 1rem; padding: 0.5rem 1rem;"><?php echo htmlspecialchars($order['status']); ?></span></p>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="orders.php" class="btn btn-primary">View My Orders</a>
        <a href="products.php" class="btn btn-outline-secondary">Continue Shopping</a>
    </div>
</div>

<?php
$conn->close();
require_once 'includes/footer.php';
?>

