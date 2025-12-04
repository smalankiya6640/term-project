<?php
$pageTitle = 'Checkout - Online Computer Store';
require_once 'config/database.php';
require_once 'config/auth.php';
requireLogin();
require_once 'includes/header.php';

$conn = getDBConnection();
$user_id = getCurrentUserId();

// Get cart items
$stmt = $conn->prepare("
    SELECT c.id, c.quantity, p.id as product_id, p.name, p.price, p.stock
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cartItems = $stmt->get_result();

if ($cartItems->num_rows == 0) {
    header('Location: cart.php');
    exit();
}

$total = 0;
$items = [];
while ($item = $cartItems->fetch_assoc()) {
    if ($item['quantity'] > $item['stock']) {
        $error = "Insufficient stock for {$item['name']}";
        break;
    }
    $subtotal = $item['price'] * $item['quantity'];
    $total += $subtotal;
    $items[] = $item;
}

$stmt->close();

// Process order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order']) && !isset($error)) {
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Create order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'pending')");
        $stmt->bind_param("id", $user_id, $total);
        $stmt->execute();
        $order_id = $conn->insert_id;
        $stmt->close();
        
        // Create order items and update stock
        foreach ($items as $item) {
            // Insert order item
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt->execute();
            $stmt->close();
            
            // Update product stock
            $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
            $stmt->execute();
            $stmt->close();
        }
        
        // Clear cart
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        header("Location: order_success.php?order_id=$order_id");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Order processing failed. Please try again.";
    }
}

// Recalculate total for display
$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h2 class="mb-0"><i class="bi bi-cart-check"></i> Checkout</h2>
    <a href="cart.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Cart
    </a>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
    <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
<?php else: ?>
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Order Summary</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Order Total</h5>
                </div>
                <div class="card-body">
                    <p class="fs-4"><strong>Total: $<?php echo number_format($total, 2); ?></strong></p>
                    <form method="POST" action="" id="checkoutForm">
                        <button type="submit" name="place_order" class="btn btn-primary btn-lg w-100" 
                                onclick="return confirm('Are you sure you want to place this order?')">
                            Place Order
                        </button>
                    </form>
                    <a href="cart.php" class="btn btn-outline-secondary w-100 mt-2">Back to Cart</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$conn->close();
require_once 'includes/footer.php';
?>

