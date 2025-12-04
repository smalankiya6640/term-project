-- Online Computer Store Database Schema
-- Created for Web Term Project

-- Create database
CREATE DATABASE IF NOT EXISTS online_computer_store;
USE online_computer_store;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    category VARCHAR(50) NOT NULL,
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, product_id)
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table (to store individual items in each order)
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert sample admin user (password: admin123)
-- Note: If login fails, run fix_admin_password.php to reset the password
INSERT INTO users (name, email, password, is_admin) VALUES
('Admin User', 'admin@store.com', '$2y$10$aFGxrjWR/M9kTS.JEvd5Wunwi5KkzhxLOjsmVrzjUKMR/W7EFlPK.', 1);

-- Insert sample products
INSERT INTO products (name, description, price, image_url, category, stock) VALUES
('Gaming Laptop Pro', 'High-performance gaming laptop with RTX 4070, 16GB RAM, 1TB SSD', 1299.99, 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=300&fit=crop', 'Laptops', 15),
('Business Desktop', 'Reliable desktop computer for office work, Intel i7, 16GB RAM, 512GB SSD', 899.99, 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=300&fit=crop', 'Desktops', 20),
('RTX 4090 Graphics Card', 'NVIDIA GeForce RTX 4090 24GB GDDR6X', 1599.99, 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=400&h=300&fit=crop', 'Graphics Cards', 8),
('DDR5 32GB RAM Kit', '32GB DDR5 5600MHz Memory Kit (2x16GB)', 199.99, 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=400&h=300&fit=crop', 'Memory', 30),
('Mechanical Keyboard', 'RGB Mechanical Gaming Keyboard with Cherry MX switches', 129.99, 'https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=400&h=300&fit=crop', 'Accessories', 50),
('Gaming Mouse', 'Wireless gaming mouse with 16000 DPI sensor', 79.99, 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=400&h=300&fit=crop', 'Accessories', 45),
('UltraWide Monitor', '34-inch 4K UltraWide Curved Monitor', 599.99, 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&h=300&fit=crop', 'Monitors', 12),
('SSD 1TB', '1TB NVMe M.2 SSD with 3500MB/s read speed', 89.99, 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=400&h=300&fit=crop', 'Storage', 40),
('Gaming Laptop Basic', 'Entry-level gaming laptop with RTX 3060, 8GB RAM, 512GB SSD', 799.99, 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=300&fit=crop', 'Laptops', 18),
('Workstation Desktop', 'Professional workstation with Intel i9, 32GB RAM, 2TB SSD', 1999.99, 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&h=300&fit=crop', 'Desktops', 10),
('RTX 4070 Graphics Card', 'NVIDIA GeForce RTX 4070 12GB GDDR6X', 599.99, 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=400&h=300&fit=crop', 'Graphics Cards', 15),
('DDR4 16GB RAM Kit', '16GB DDR4 3200MHz Memory Kit (2x8GB)', 69.99, 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=400&h=300&fit=crop', 'Memory', 35);

