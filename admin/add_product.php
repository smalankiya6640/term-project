<?php
$pageTitle = 'Add Product - Admin Panel';
require_once '../config/database.php';
require_once '../config/auth.php';
requireAdmin();
require_once '../includes/header.php';

$error = '';
$success = '';

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
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image_url, category, stock) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdssi", $name, $description, $price, $image_url, $category, $stock);
        
        if ($stmt->execute()) {
            $success = 'Product added successfully!';
            // Clear form
            $_POST = [];
        } else {
            $error = 'Failed to add product. Please try again.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

// Get categories for dropdown
$conn = getDBConnection();
$categoriesResult = $conn->query("SELECT DISTINCT category FROM products ORDER BY category");
$categories = [];
while ($row = $categoriesResult->fetch_assoc()) {
    $categories[] = $row['category'];
}
$conn->close();
?>

<h2 class="mb-4">Add New Product</h2>

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
                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                        <div class="invalid-feedback">Please provide a product name.</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        <div class="invalid-feedback">Please provide a product description.</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price ($)</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" 
                                   value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" min="0.01" required>
                            <div class="invalid-feedback">Price must be greater than 0.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" id="stock" name="stock" 
                                   value="<?php echo htmlspecialchars($_POST['stock'] ?? 0); ?>" min="0" required>
                            <div class="invalid-feedback">Stock must be 0 or greater.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="category" name="category" 
                               list="categories" value="<?php echo htmlspecialchars($_POST['category'] ?? ''); ?>" required>
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
                               value="<?php echo htmlspecialchars($_POST['image_url'] ?? ''); ?>" required>
                        <div class="invalid-feedback">Please provide a valid image URL.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Product</button>
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

