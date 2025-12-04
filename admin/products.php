<?php
$pageTitle = 'Manage Products - Admin Panel';
require_once '../config/database.php';
require_once '../config/auth.php';
requireAdmin();
require_once '../includes/header.php';

$conn = getDBConnection();

// Handle delete
if (isset($_GET['delete'])) {
    $product_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();
    header('Location: products.php');
    exit();
}

// Get all products
$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h2 class="mb-0"><i class="bi bi-box-seam"></i> Manage Products</h2>
    <a href="add_product.php" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Add New Product
    </a>
</div>


<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($product = $products->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td>
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 0.375rem;"
                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'60\' height=\'60\'%3E%3Crect fill=\'%23e2e8f0\' width=\'60\' height=\'60\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'Arial\' font-size=\'8\' fill=\'%2364748b\'%3EImage%3C/text%3E%3C/svg%3E';">
                    </td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                    <td>
                        <?php if ($product['stock'] > 0): ?>
                            <span class="badge bg-success"><?php echo $product['stock']; ?></span>
                        <?php else: ?>
                            <span class="badge bg-danger">0</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="products.php?delete=<?php echo $product['id']; ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Are you sure you want to delete this product?')">
                            <i class="bi bi-trash"></i> Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
$conn->close();
require_once '../includes/footer.php';
?>

