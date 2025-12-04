<?php
$pageTitle = 'Products - Online Computer Store';
require_once 'config/database.php';
require_once 'config/auth.php';
require_once 'includes/header.php';

$conn = getDBConnection();

// Get category filter
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();

// Get all categories for filter
$categoriesResult = $conn->query("SELECT DISTINCT category FROM products ORDER BY category");
$categories = [];
while ($row = $categoriesResult->fetch_assoc()) {
    $categories[] = $row['category'];
}
?>

<div class="row mb-4 fade-in">
    <div class="col-md-12">
        <h2><i class="bi bi-grid"></i> Browse Products</h2>
    </div>
</div>

<!-- Search and Filter -->
<div class="row mb-4">
    <div class="col-md-6 position-relative">
        <input type="text" class="form-control" id="live-search" 
               placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
        <div id="search-results" class="search-results mt-1" style="display: none;"></div>
    </div>
    <div class="col-md-6">
        <form method="GET" action="products.php" class="d-flex">
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
            <select name="category" id="category-filter" class="form-select me-2" onchange="this.form.submit(); filterProducts();">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" 
                            <?php echo $category === $cat ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
</div>

<!-- Client-side Filters -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="filter-card">
            <h5 class="mb-3"><i class="bi bi-funnel"></i> Filter Products</h5>
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Price Range</label>
                        <div class="d-flex gap-2">
                            <input type="number" id="price-min" class="form-control" placeholder="Min" min="0" onchange="filterProducts()">
                            <input type="number" id="price-max" class="form-control" placeholder="Max" min="0" onchange="filterProducts()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="in-stock" onchange="filterProducts()">
                            <label class="form-check-label" for="in-stock">
                                In Stock Only
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-secondary mt-4" onclick="clearFilters()">Clear Filters</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Products Grid -->
<div class="row">
    <?php if ($products->num_rows > 0): ?>
        <?php while ($product = $products->fetch_assoc()): ?>
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
                            <?php if ($product['stock'] > 0): ?>
                                <span class="badge bg-success ms-2">In Stock</span>
                            <?php else: ?>
                                <span class="badge bg-danger ms-2">Out of Stock</span>
                            <?php endif; ?>
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
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">No products found.</div>
        </div>
    <?php endif; ?>
    <div id="no-results" class="col-12" style="display: none;">
        <div class="alert alert-warning">No products match your filters. Try adjusting your search criteria.</div>
    </div>
</div>

<script>
function clearFilters() {
    document.getElementById('price-min').value = '';
    document.getElementById('price-max').value = '';
    document.getElementById('in-stock').checked = false;
    filterProducts();
}
</script>

<?php
$stmt->close();
$conn->close();
require_once 'includes/footer.php';
?>

