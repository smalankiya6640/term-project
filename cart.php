<?php
$pageTitle = 'Shopping Cart - Online Computer Store';
require_once 'config/database.php';
require_once 'config/auth.php';
requireLogin();
require_once 'includes/header.php';

$conn = getDBConnection();
$user_id = getCurrentUserId();

// Handle remove item
if (isset($_GET['remove'])) {
    $cart_id = intval($_GET['remove']);
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header('Location: cart.php');
    exit();
}

// Handle update quantity
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $cart_id => $quantity) {
        $cart_id = intval($cart_id);
        $quantity = intval($quantity);
        
        if ($quantity > 0) {
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $cart_id, $user_id);
            $stmt->execute();
            $stmt->close();
        }
    }
    header('Location: cart.php');
    exit();
}

// Get cart items
$stmt = $conn->prepare("
    SELECT c.id, c.quantity, p.id as product_id, p.name, p.price, p.image_url, p.stock, p.category
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cartItems = $stmt->get_result();

$total = 0;
?>

<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h2 class="mb-0">Shopping Cart</h2>
    <a href="products.php" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left"></i> Continue Shopping
    </a>
</div>

<?php if ($cartItems->num_rows > 0): ?>
    <form method="POST" action="">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $cartItems->fetch_assoc()): ?>
                        <?php
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                        ?>
                        <tr id="cart-item-<?php echo $item['id']; ?>">
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                         class="me-3" style="width: 80px; height: 80px; object-fit: cover; border-radius: 0.5rem;"
                                         onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'80\' height=\'80\'%3E%3Crect fill=\'%23e2e8f0\' width=\'80\' height=\'80\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'Arial\' font-size=\'10\' fill=\'%2364748b\'%3E<?php echo htmlspecialchars($item['name']); ?>%3C/text%3E%3C/svg%3E';">
                                    <div>
                                        <h6><?php echo htmlspecialchars($item['name']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($item['category']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <input type="number" 
                                       name="quantity[<?php echo $item['id']; ?>]" 
                                       value="<?php echo $item['quantity']; ?>" 
                                       min="1" 
                                       max="<?php echo $item['stock']; ?>" 
                                       class="form-control cart-quantity" 
                                       data-cart-id="<?php echo $item['id']; ?>"
                                       data-price="<?php echo $item['price']; ?>"
                                       style="width: 80px;"
                                       onchange="updateCartQuantity(<?php echo $item['id']; ?>, this.value)">
                            </td>
                            <td class="subtotal">$<?php echo number_format($subtotal, 2); ?></td>
                            <td>
                                <button type="button" 
                                        onclick="removeFromCart(<?php echo $item['id']; ?>)" 
                                        class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Remove
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td><strong id="cart-total">$<?php echo number_format($total, 2); ?></strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="mt-3">
            <button type="submit" name="update_cart" class="btn btn-secondary">Update Cart</button>
            <a href="checkout.php" class="btn btn-primary btn-lg ms-2">Proceed to Checkout</a>
        </div>
    </form>
<?php else: ?>
    <div class="alert alert-info">
        <h4>Your cart is empty</h4>
        <p>Start shopping to add items to your cart!</p>
        <a href="products.php" class="btn btn-primary">Browse Products</a>
    </div>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
require_once 'includes/footer.php';
?>

