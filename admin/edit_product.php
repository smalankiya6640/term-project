<?php
$pageTitle = 'Edit Product - Admin Panel';
require_once '../config/database.php';
require_once '../config/auth.php';
requireAdmin();
require_once '../includes/header.php';

$product_id = $_GET['id'] ?? 0;

if (!$product_id) {
    header('Location: products.php');
    exit();
}

$conn = getDBConnection();
$error = '';
$success = '';

// Get product
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    header('Location: products.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $image_url = trim($_POST['image_url'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $stock = intval($_POST['stock'] ?? 0);
    
    // Validation
    if (empty($name) || empty($description) || $price <= 0 || empty($image_url) || empty($category)) {
        $error = 'All fields are required and price must be greater than 0.';
    } else {
        $updateStmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, image_url = ?, category = ?, stock = ? WHERE id = ?");
        $updateStmt->bind_param("ssdssii", $name, $description, $price, $image_url, $category, $stock, $product_id);
        
        if ($updateStmt->execute()) {
            $updateStmt->close();
            $success = 'Product updated successfully!';
            // Refresh product data
            $selectStmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $selectStmt->bind_param("i", $product_id);
            $selectStmt->execute();
            $product = $selectStmt->get_result()->fetch_assoc();
            $selectStmt->close();
        } else {
            $error = 'Failed to update product. Please try again.';
            $updateStmt->close();
        }
    }
}

// Get categories for dropdown
$categoriesResult = $conn->query("SELECT DISTINCT category FROM products ORDER BY category");
$categories = [];
while ($row = $categoriesResult->fetch_assoc()) {
    $categories[] = $row['category'];
}
$conn->close();
?>

<h2 class="mb-4">Edit Product</h2>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="" id="productForm" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        <div class="invalid-feedback">Please provide a product name.</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                        <div class="invalid-feedback">Please provide a product description.</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price ($)</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" 
                                   value="<?php echo $product['price']; ?>" min="0.01" required>
                            <div class="invalid-feedback">Price must be greater than 0.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" id="stock" name="stock" 
                                   value="<?php echo $product['stock']; ?>" min="0" required>
                            <div class="invalid-feedback">Stock must be 0 or greater.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="category" name="category" 
                               list="categories" value="<?php echo htmlspecialchars($product['category']); ?>" required>
                        <datalist id="categories">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>">
                            <?php endforeach; ?>
                        </datalist>
                        <div class="invalid-feedback">Please provide a category.</div>
                    </div>
                    <div class="mb-3">
                        <label for="image_url" class="form-label">Image URL</label>
                        <input type="url" class="form-control" id="image_url" name="image_url" 
                               value="<?php echo htmlspecialchars($product['image_url']); ?>" required>
                        <div class="invalid-feedback">Please provide a valid image URL.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                    <a href="products.php" class="btn btn-secondary">Cancel</a>
                </form>
                
                <script>
                // Client-side form validation
                document.getElementById('productForm').addEventListener('submit', function(e) {
                    const name = document.getElementById('name');
                    const description = document.getElementById('description');
                    const price = document.getElementById('price');
                    const stock = document.getElementById('stock');
                    const category = document.getElementById('category');
                    const imageUrl = document.getElementById('image_url');
                    let isValid = true;
                    
                    if (!name.value.trim()) {
                        name.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        name.classList.remove('is-invalid');
                    }
                    
                    if (!description.value.trim()) {
                        description.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        description.classList.remove('is-invalid');
                    }
                    
                    if (!price.value || parseFloat(price.value) <= 0) {
                        price.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        price.classList.remove('is-invalid');
                    }
                    
                    if (stock.value === '' || parseInt(stock.value) < 0) {
                        stock.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        stock.classList.remove('is-invalid');
                    }
                    
                    if (!category.value.trim()) {
                        category.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        category.classList.remove('is-invalid');
                    }
                    
                    if (!imageUrl.value || !imageUrl.validity.valid) {
                        imageUrl.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        imageUrl.classList.remove('is-invalid');
                    }
                    
                    if (!isValid) {
                        e.preventDefault();
                    }
                });
                </script>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

