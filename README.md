# Online Computer Store - Web Term Project

A full-stack web application for browsing, searching, and purchasing computer products. Built with HTML5, CSS (Bootstrap), JavaScript, PHP, and MySQL.

## Project Description

This is a complete e-commerce platform that allows users to:
- Browse computer products by category (laptops, desktops, graphics cards, memory, accessories, etc.)
- Register and login to their accounts
- Add products to shopping cart
- Place orders and view order history
- Search and filter products

Admin users can:
- Manage product listings (add, edit, delete)
- View all user orders
- Update order status

## Technologies Used

- **HTML5** - Web page structure
- **CSS / Bootstrap 5** - Styling and responsive layout
- **JavaScript** - Client-side interaction and validation
- **PHP** - Server-side processing
- **MySQL** - Database backend
- **XAMPP / LAMP** - Local server development environment

## Features

### User Features
- ✅ User registration and login/logout
- ✅ Browse products by category
- ✅ Product search functionality
- ✅ View product details
- ✅ Add products to cart
- ✅ Remove items from cart
- ✅ Update cart quantities
- ✅ Checkout and place orders
- ✅ View order history
- ✅ Responsive design

### Admin Features
- ✅ Admin login
- ✅ Add/edit/delete products
- ✅ View all user orders
- ✅ Update order status
- ✅ Admin dashboard with statistics

### Security Features
- ✅ Password hashing using `password_hash()`
- ✅ SQL injection protection using prepared statements
- ✅ Session management for authentication
- ✅ Input validation (client-side and server-side)
- ✅ XSS protection using `htmlspecialchars()`

## Setup Instructions

### Prerequisites
- XAMPP (or LAMP/WAMP) installed
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web browser

### Installation Steps

1. **Clone or download the repository**
   ```bash
   git clone <repository-url>
   cd online_store
   ```

2. **Start XAMPP**
   - Start Apache and MySQL services from XAMPP Control Panel

3. **Import the database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database or use existing MySQL
   - Import the `database.sql` file:
     - Click on "Import" tab
     - Choose file: `database.sql`
     - Click "Go"

4. **Configure database connection** (if needed)
   - Edit `config/database.php` if your MySQL credentials are different:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_PORT', 3308);  // Change if your MySQL uses a different port
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'online_computer_store');
     ```
   - **Note:** The default MySQL port is 3306. If your MySQL runs on port 3308 (as configured), make sure the port matches your XAMPP MySQL configuration.

5. **Place project in web server directory**
   - Copy the project folder to `C:\xampp\htdocs\` (Windows) or `/var/www/html/` (Linux)
   - Or use the project folder directly if using XAMPP's htdocs

6. **Access the application**
   - Open your web browser
   - Navigate to: `http://localhost/online_store/`
   - Or: `http://localhost/online_store/index.php`

7. **Set up admin password (Optional)**
   - If you need to reset the admin password, run from command line:
     ```bash
     php setup_admin.php
     ```
   - Or manually update in phpMyAdmin if needed

### Default Admin Account
- **Email:** admin@store.com
- **Password:** admin123

**Note:** 
- The default admin account is created automatically when you import the database.
- If you need to reset the admin password, you can:
  1. Run `php setup_admin.php` from the command line, or
  2. Manually update the password in the database using phpMyAdmin
- For production, change the default admin password immediately!

## Project Structure

```
online_store/
├── config/
│   ├── database.php      # Database configuration
│   └── auth.php          # Authentication helpers
├── includes/
│   ├── header.php        # Common header/navigation
│   └── footer.php        # Common footer
├── admin/
│   ├── index.php         # Admin dashboard
│   ├── products.php      # Product management
│   ├── add_product.php   # Add new product
│   ├── edit_product.php  # Edit product
│   ├── orders.php        # View all orders
│   └── order_details.php # Order details
├── index.php             # Homepage
├── products.php          # Browse products
├── product.php           # Product details
├── cart.php              # Shopping cart
├── checkout.php          # Checkout page
├── order_success.php     # Order confirmation
├── orders.php            # User order history
├── order_details.php     # Order details (user)
├── login.php             # User login
├── register.php          # User registration
├── logout.php            # Logout handler
├── database.sql          # Database schema and sample data
└── README.md             # This file
```

## Database Schema

### Tables
- **users** - User accounts (id, name, email, password, is_admin)
- **products** - Product catalog (id, name, description, price, image_url, category, stock)
- **cart** - Shopping cart items (id, user_id, product_id, quantity)
- **orders** - Order records (id, user_id, total_price, order_date, status)
- **order_items** - Individual items in orders (id, order_id, product_id, quantity, price)

## Usage

### For Users
1. Register a new account or login
2. Browse products on the homepage or products page
3. Use search and category filters to find products
4. Click on a product to view details
5. Add products to cart
6. Review cart and proceed to checkout
7. View order history in "My Orders"

### For Admins
1. Login with admin credentials
2. Access Admin Panel from navigation
3. Manage products (add, edit, delete)
4. View all orders and update order status
5. Monitor dashboard statistics

## Security Notes

- All passwords are hashed using PHP's `password_hash()` function
- All SQL queries use prepared statements to prevent SQL injection
- User input is sanitized using `htmlspecialchars()` to prevent XSS
- Session-based authentication for secure access control
- Admin routes are protected with `requireAdmin()` function

## Browser Compatibility

- Chrome (latest)
- Firefox (latest)
- Edge (latest)
- Safari (latest)
- Mobile browsers (responsive design)

## Future Enhancements (Optional Features)

- Product reviews and ratings
- Advanced search with multiple filters
- Email notifications for orders
- Payment gateway integration
- User profile management
- Wishlist functionality
- Product recommendations

## Troubleshooting

### Database Connection Error
- Ensure MySQL is running in XAMPP
- Check database credentials in `config/database.php`
- Verify database port matches your MySQL configuration (default: 3306, configured: 3308)
- Verify database name matches in SQL file
- Check if MySQL service is accessible on the specified port

### Session Issues
- Ensure `session_start()` is called before any output
- Check PHP session configuration
- Clear browser cookies if needed

### Image Not Displaying
- Verify image URLs are accessible
- Check placeholder image URLs in database
- Ensure internet connection for external images

## License

This project is created for educational purposes as part of a Web Term Project.

## Author

Sujal Malankiya
249567880

## Submission Date

5th december 2025

---

**Note:** This is a development project. For production deployment, additional security measures, error handling, and optimizations should be implemented.


