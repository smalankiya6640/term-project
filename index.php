<?php
$pageTitle = 'Home - Online Computer Store';
require_once 'config/database.php';
require_once 'config/auth.php';
require_once 'includes/header.php';

$conn = getDBConnection();

// Get all products
$stmt = $conn->prepare("SELECT * FROM products ORDER BY created_at DESC");
$stmt->execute();
$featuredProducts = $stmt->get_result();
?>

<div class="jumbotron bg-primary text-white p-5 rounded mb-5 fade-in">
    <h1 class="display-4">Welcome to Online Computer Store</h1>
    <p class="lead">Your one-stop shop for computers, components, and accessories</p>
    <a class="btn btn-light btn-lg mt-3" href="products.php" role="button">
        <i class="bi bi-bag"></i> Browse Products
    </a>
</div>

<h2 class="mb-4 fade-in">Featured Products</h2>
<div class="row">
    <?php while ($product = $featuredProducts->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
            <div class="card product-card"
                 data-category="<?php echo htmlspecialchars($product['category']); ?>"
                 data-price="<?php echo $product['price']; ?>"
                 data-stock="<?php echo $product['stock']; ?>">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                     class="card-img-top product-image" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'%3E%3Crect fill=\'%23e2e8f0\' width=\'400\' height=\'300\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'Arial\' font-size=\'20\' fill=\'%2364748b\'%3E<?php echo htmlspecialchars($product['name']); ?>%3C/text%3E%3C/svg%3E';">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                    <p class="card-text">
                        <strong class="text-primary">$<?php echo number_format($product['price'], 2); ?></strong>
                        <span class="badge bg-secondary ms-2"><?php echo htmlspecialchars($product['category']); ?></span>
                    </p>
                    <div class="d-flex gap-2">
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary flex-fill">View Details</a>
                        <?php if (isLoggedIn() && $product['stock'] > 0): ?>
                            <button onclick="addToCart(<?php echo $product['id']; ?>, 1, this)" class="btn btn-success" title="Quick Add to Cart">
                                <i class="bi bi-cart-plus"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php
$stmt->close();
$conn->close();
require_once 'includes/footer.php';
?>

