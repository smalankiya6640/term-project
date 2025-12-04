    </main>
    <footer class="text-center">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5 class="mb-3"><i class="bi bi-laptop"></i> Computer Store</h5>
                    <p class="mb-0">Your trusted partner for all your computing needs</p>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo $basePath; ?>index.php" class="text-white text-decoration-none">Home</a></li>
                        <li><a href="<?php echo $basePath; ?>products.php" class="text-white text-decoration-none">Products</a></li>
                        <?php if (isLoggedIn()): ?>
                            <li><a href="<?php echo $basePath; ?>cart.php" class="text-white text-decoration-none">Cart</a></li>
                            <li><a href="<?php echo $basePath; ?>orders.php" class="text-white text-decoration-none">My Orders</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Contact</h5>
                    <p class="mb-1"><i class="bi bi-envelope"></i> support@computerstore.com</p>
                    <p class="mb-0"><i class="bi bi-telephone"></i> +1 (555) 123-4567</p>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">
            <div class="row">
                <div class="col-12">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Online Computer Store. All rights reserved.</p>
                    <p class="mb-0 mt-2" style="opacity: 0.8; font-size: 0.9rem;">Web Term Project - Full Stack Web Application</p>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

