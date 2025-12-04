<?php
$pageTitle = 'My Orders - Online Computer Store';
require_once 'config/database.php';
require_once 'config/auth.php';
requireLogin();
require_once 'includes/header.php';

$conn = getDBConnection();
$user_id = getCurrentUserId();

// Get user orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h2 class="mb-0"><i class="bi bi-receipt"></i> My Orders</h2>
    <a href="products.php" class="btn btn-primary">
        <i class="bi bi-bag"></i> Continue Shopping
    </a>
</div>

<?php if ($orders->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo date('F j, Y g:i A', strtotime($order['order_date'])); ?></td>
                        <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                        <td>
                            <span class="badge bg-info"><?php echo htmlspecialchars($order['status']); ?></span>
                        </td>
                        <td>
                            <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                View Details
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <h4>No orders yet</h4>
        <p>Start shopping to place your first order!</p>
        <a href="products.php" class="btn btn-primary">Browse Products</a>
    </div>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
require_once 'includes/footer.php';
?>

