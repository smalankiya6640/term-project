<?php
$pageTitle = 'Product Details - Online Computer Store';
require_once 'config/database.php';
require_once 'config/auth.php';
require_once 'includes/header.php';

$product_id = $_GET['id'] ?? 0;

if (!$product_id) {
    header('Location: products.php');
    exit();
}

$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: products.php');
    exit();
}

$product = $result->fetch_assoc();
$stmt->close();

$added = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
    
    $quantity = intval($_POST['quantity'] ?? 1);
    $user_id = getCurrentUserId();
    
    if ($quantity > 0 && $quantity <= $product['stock']) {
        // Check if item already in cart
        $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $cartItem = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($cartItem) {
            // Update quantity
            $newQuantity = $cartItem['quantity'] + $quantity;
            if ($newQuantity <= $product['stock']) {
                $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
                $stmt->bind_param("ii", $newQuantity, $cartItem['id']);
                $stmt->execute();
                $stmt->close();
                $added = true;
            }
        } else {
            // Insert new cart item
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
            $stmt->execute();
            $stmt->close();
            $added = true;
        }
    }
}
$conn->close();
?>

<div class="row">
    <div class="col-md-6">
        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
             class="img-fluid rounded product-detail-image" 
             alt="<?php echo htmlspecialchars($product['name']); ?>"
             style="cursor: pointer;"
             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'600\' height=\'600\'%3E%3Crect fill=\'%23e2e8f0\' width=\'600\' height=\'600\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'Arial\' font-size=\'24\' fill=\'%2364748b\'%3E<?php echo htmlspecialchars($product['name']); ?>%3C/text%3E%3C/svg%3E';">
    </div>
    <div class="col-md-6 fade-in">
        <h2 class="mb-3" style="font-weight: 800;"><?php echo htmlspecialchars($product['name']); ?></h2>
        <div class="mb-3">
            <span class="badge bg-secondary me-2"><?php echo htmlspecialchars($product['category']); ?></span>
            <?php if ($product['stock'] > 0): ?>
                <span class="badge bg-success">In Stock (<?php echo $product['stock']; ?> available)</span>
            <?php else: ?>
                <span class="badge bg-danger">Out of Stock</span>
            <?php endif; ?>
        </div>
        <h3 class="text-primary mb-4" style="font-weight: 800; font-size: 2.5rem;">$<?php echo number_format($product['price'], 2); ?></h3>
        <div class="product-description mt-4 p-3" style="background: #f8fafc; border-radius: 0.75rem; border-left: 4px solid var(--primary-color);">
            <h5 class="mb-3" style="font-weight: 700;">Description</h5>
            <p class="mb-0" style="line-height: 1.8;"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        </div>
        
        <?php if ($added): ?>
            <div class="alert alert-success">Product added to cart!</div>
        <?php endif; ?>
        
        <?php if (isLoggedIn()): ?>
            <?php if ($product['stock'] > 0): ?>
                <div class="mt-4">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" 
                               value="1" min="1" max="<?php echo $product['stock']; ?>" required>
                    </div>
                    <button type="button" onclick="addToCart(<?php echo $product['id']; ?>, document.getElementById('quantity').value, this)" 
                            class="btn btn-primary btn-lg">
                        <i class="bi bi-cart-plus"></i> Add to Cart
                    </button>
                </div>
            <?php else: ?>
                <button class="btn btn-secondary btn-lg" disabled>Out of Stock</button>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-muted">Please <a href="login.php">login</a> to add items to cart.</p>
        <?php endif; ?>
        
        <a href="products.php" class="btn btn-outline-secondary mt-3">Back to Products</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

