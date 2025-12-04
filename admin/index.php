<?php
$pageTitle = 'Admin Dashboard - Online Computer Store';
require_once '../config/database.php';
require_once '../config/auth.php';
requireAdmin();
require_once '../includes/header.php';

$conn = getDBConnection();

// Get statistics
$totalProducts = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$totalOrders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_admin = 0")->fetch_assoc()['count'];
$totalRevenue = $conn->query("SELECT SUM(total_price) as total FROM orders")->fetch_assoc()['total'] ?? 0;

// Get recent orders
$recentOrders = $conn->query("
    SELECT o.*, u.name as user_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.order_date DESC 
    LIMIT 5
");
?>

<h2 class="mb-4">Admin Dashboard</h2>

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="admin-card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-box-seam"></i> Total Products</h5>
                <h2><?php echo $totalProducts; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="admin-card text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-cart-check"></i> Total Orders</h5>
                <h2><?php echo $totalOrders; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="admin-card text-white" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-people"></i> Total Users</h5>
                <h2><?php echo $totalUsers; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="admin-card text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-currency-dollar"></i> Total Revenue</h5>
                <h2>$<?php echo number_format($totalRevenue, 2); ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Recent Orders</h5>
                <a href="orders.php" class="btn btn-sm btn-primary">View All Orders</a>
            </div>
            <div class="card-body">
                <?php if ($recentOrders->num_rows > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $recentOrders->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></td>
                                    <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                                    <td><span class="badge bg-info"><?php echo htmlspecialchars($order['status']); ?></span></td>
                                    <td>
                                        <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No orders yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="products.php" class="btn btn-primary me-2">Manage Products</a>
                <a href="add_product.php" class="btn btn-success me-2">Add New Product</a>
                <a href="orders.php" class="btn btn-info">View All Orders</a>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close();
require_once '../includes/footer.php';
?>

