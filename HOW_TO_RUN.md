# How to Run the Online Store - Step by Step Guide

## Step 1: Start XAMPP Services
1. Open **XAMPP Control Panel** (search for "XAMPP" in Spotlight/Applications)
2. Click **Start** button next to **Apache**
3. Click **Start** button next to **MySQL**
4. Wait until both show "Running" status (green)

## Step 2: Set Up the Database
1. Open your web browser
2. Go to: `http://localhost/phpmyadmin`
3. Click on **"Import"** tab at the top
4. Click **"Choose File"** button
5. Navigate to your project folder: `/Users/sujalmalankiya/Downloads/online_store/`
6. Select the file: `database.sql`
7. Click **"Go"** button at the bottom
8. Wait for "Import has been successfully finished" message

## Step 3: Start the PHP Development Server
1. Open **Terminal** (Applications > Utilities > Terminal)
2. Type this command and press Enter:
   ```bash
   cd /Users/sujalmalankiya/Downloads/online_store
   ```
3. Then type this command and press Enter:
   ```bash
   php -S localhost:8000
   ```
4. You should see: `PHP 8.x.x Development Server started at http://localhost:8000`

## Step 4: Open the Application
1. Open your web browser (Chrome, Safari, Firefox, etc.)
2. Go to: `http://localhost:8000`
3. You should see the Online Computer Store homepage!

## Step 5: Login (Optional)
- **Admin Login:**
  - Email: `admin@store.com`
  - Password: `admin123`
- **Or create a new user account** by clicking "Register"

---

## Quick Commands Summary

**To start the server:**
```bash
cd /Users/sujalmalankiya/Downloads/online_store
php -S localhost:8000
```

**To stop the server:**
- Press `Ctrl + C` in the Terminal window

**To access the application:**
- Open browser: `http://localhost:8000`

---

## Troubleshooting

**If you see "Database connection failed":**
- Make sure MySQL is running in XAMPP Control Panel
- Check that the database was imported successfully in phpMyAdmin

**If port 8000 is already in use:**
- Use a different port: `php -S localhost:8001`
- Then access: `http://localhost:8001`

**If PHP command not found:**
- Install PHP via Homebrew: `brew install php`
- Or use XAMPP's PHP: `/Applications/XAMPP/xamppfiles/bin/php -S localhost:8000`

