// Main JavaScript file for Online Computer Store
// Handles dynamic interactions, AJAX requests, and UI enhancements

// Cart Management Functions
function addToCart(productId, quantity = 1, buttonElement = null) {
    if (!productId) {
        alert('Product ID is required');
        return;
    }
    
    // Get button element
    const btn = buttonElement || (window.event ? window.event.target : null);
    
    // Show loading state
    let originalText = '';
    if (btn) {
        originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Adding...';
    }
    
    // Ensure quantity is a number
    quantity = parseInt(quantity) || 1;
    
    // Create form data
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    formData.append('ajax', '1');
    
    // Determine correct path for AJAX
    const basePath = window.location.pathname.includes('/admin/') ? '../' : '';
    const ajaxUrl = basePath + 'ajax/add_to_cart.php';
    
    // Send AJAX request
    fetch(ajaxUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Product added to cart!', 'success');
            // Update cart count in navbar
            updateCartCount();
        } else {
            alert(data.message || 'Failed to add product to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again. Error: ' + error.message);
    })
    .finally(() => {
        // Reset button
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
}

// Update cart count in navbar
function updateCartCount() {
    const basePath = window.location.pathname.includes('/admin/') ? '../' : '';
    const ajaxUrl = basePath + 'ajax/get_cart_count.php';
    
    fetch(ajaxUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const cartBadge = document.getElementById('cart-count');
            if (cartBadge) {
                if (data.count > 0) {
                    cartBadge.textContent = data.count;
                    cartBadge.style.display = 'inline';
                } else {
                    cartBadge.style.display = 'none';
                }
            }
        })
        .catch(error => console.error('Error updating cart count:', error));
}

// Remove item from cart via AJAX
function removeFromCart(cartId) {
    if (!confirm('Remove this item from cart?')) {
        return;
    }
    
    const basePath = window.location.pathname.includes('/admin/') ? '../' : '';
    const ajaxUrl = `${basePath}ajax/remove_from_cart.php?id=${cartId}`;
    
    fetch(ajaxUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Remove row from table
                const row = document.getElementById(`cart-item-${cartId}`);
                if (row) {
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                        updateCartTotal();
                        updateCartCount();
                        // Check if cart is empty
                        if (document.querySelectorAll('tbody tr').length === 0) {
                            location.reload();
                        }
                    }, 300);
                }
                showNotification('Item removed from cart', 'success');
            } else {
                alert(data.message || 'Failed to remove item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
}

// Update cart quantity dynamically
function updateCartQuantity(cartId, quantity) {
    if (quantity < 1) {
        removeFromCart(cartId);
        return;
    }
    
    const basePath = window.location.pathname.includes('/admin/') ? '../' : '';
    const ajaxUrl = basePath + 'ajax/update_cart.php';
    
    fetch(ajaxUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cart_id: cartId,
            quantity: quantity
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update subtotal
            const row = document.getElementById(`cart-item-${cartId}`);
            if (row) {
                const subtotalCell = row.querySelector('.subtotal');
                if (subtotalCell) {
                    subtotalCell.textContent = '$' + parseFloat(data.subtotal).toFixed(2);
                }
            }
            updateCartTotal();
        } else {
            alert(data.message || 'Failed to update quantity');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// Calculate and update cart total
function updateCartTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal').forEach(cell => {
        const value = parseFloat(cell.textContent.replace('$', ''));
        if (!isNaN(value)) {
            total += value;
        }
    });
    
    const totalCell = document.getElementById('cart-total');
    if (totalCell) {
        totalCell.textContent = '$' + total.toFixed(2);
    }
}

// Live Search Functionality
function initLiveSearch() {
    const searchInput = document.getElementById('live-search');
    if (!searchInput) return;
    
    let searchTimeout;
    const searchResults = document.getElementById('search-results');
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            if (searchResults) {
                searchResults.innerHTML = '';
                searchResults.style.display = 'none';
            }
            return;
        }
        
        searchTimeout = setTimeout(() => {
            const basePath = window.location.pathname.includes('/admin/') ? '../' : '';
            const ajaxUrl = `${basePath}ajax/search_products.php?q=${encodeURIComponent(query)}`;
            
            fetch(ajaxUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (searchResults) {
                        displaySearchResults(data, searchResults);
                        searchResults.style.display = data.length > 0 ? 'block' : 'none';
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    if (searchResults) {
                        searchResults.style.display = 'none';
                    }
                });
        }, 300);
    });
    
    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (searchResults && !searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
}

// Display search results
function displaySearchResults(products, container) {
    if (products.length === 0) {
        container.innerHTML = '<div class="list-group-item text-muted">No products found</div>';
        return;
    }
    
    let html = '<div class="list-group list-group-flush">';
    products.forEach(product => {
        html += `
            <a href="product.php?id=${product.id}" class="list-group-item list-group-item-action">
                <div class="d-flex align-items-center">
                    <img src="${product.image_url}" alt="${product.name}" 
                         style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                    <div>
                        <h6 class="mb-0">${product.name}</h6>
                        <small class="text-muted">$${parseFloat(product.price).toFixed(2)}</small>
                    </div>
                </div>
            </a>
        `;
    });
    html += '</div>';
    
    container.innerHTML = html;
}

// Product Filtering
function filterProducts() {
    const category = document.getElementById('category-filter')?.value || '';
    const priceMin = document.getElementById('price-min')?.value || '';
    const priceMax = document.getElementById('price-max')?.value || '';
    const inStock = document.getElementById('in-stock')?.checked || false;
    
    const products = document.querySelectorAll('.product-card');
    let visibleCount = 0;
    
    products.forEach(card => {
        const productCategory = card.dataset.category || '';
        const productPrice = parseFloat(card.dataset.price || 0);
        const productStock = parseInt(card.dataset.stock || 0);
        
        let show = true;
        
        if (category && productCategory !== category) {
            show = false;
        }
        
        if (priceMin && productPrice < parseFloat(priceMin)) {
            show = false;
        }
        
        if (priceMax && productPrice > parseFloat(priceMax)) {
            show = false;
        }
        
        if (inStock && productStock <= 0) {
            show = false;
        }
        
        if (show) {
            card.closest('.col-md-4').style.display = 'block';
            visibleCount++;
        } else {
            card.closest('.col-md-4').style.display = 'none';
        }
    });
    
    // Show message if no products match
    const noResults = document.getElementById('no-results');
    if (noResults) {
        noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    }
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Image Lightbox
function initImageLightbox() {
    const images = document.querySelectorAll('.product-image, .product-detail-image');
    images.forEach(img => {
        img.style.cursor = 'pointer';
        img.addEventListener('click', function() {
            showLightbox(this.src, this.alt);
        });
    });
}

function showLightbox(imageSrc, imageAlt) {
    const lightbox = document.createElement('div');
    lightbox.className = 'lightbox';
    lightbox.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
        cursor: pointer;
    `;
    
    const img = document.createElement('img');
    img.src = imageSrc;
    img.alt = imageAlt;
    img.style.cssText = 'max-width: 90%; max-height: 90%; object-fit: contain;';
    
    lightbox.appendChild(img);
    document.body.appendChild(lightbox);
    
    lightbox.addEventListener('click', function() {
        document.body.removeChild(lightbox);
    });
}

// Quantity Calculator
function calculateSubtotal(price, quantity) {
    return (parseFloat(price) * parseInt(quantity)).toFixed(2);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize live search
    initLiveSearch();
    
    // Initialize image lightbox
    initImageLightbox();
    
    // Update cart count on page load
    updateCartCount();
    
    // Add smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    // Add fade-in animation to product cards
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });
    
    document.querySelectorAll('.product-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s, transform 0.5s';
        observer.observe(card);
    });
});

// Price Range Slider
function initPriceSlider() {
    const priceSlider = document.getElementById('price-range');
    const priceDisplay = document.getElementById('price-display');
    
    if (priceSlider && priceDisplay) {
        priceSlider.addEventListener('input', function() {
            priceDisplay.textContent = '$' + this.value;
            filterProducts();
        });
    }
}

