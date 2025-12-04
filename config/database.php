<?php
// Database configuration
define('DB_HOST', '127.0.0.1'); // Use 127.0.0.1 to force TCP/IP connection (works with XAMPP)
define('DB_PORT', 3306); // MySQL port
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'online_computer_store');

// Create database connection
function getDBConnection() {
    // Check if this is an AJAX request
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    $isAjax = $isAjax || (isset($_POST['ajax']) || isset($_GET['ajax']));
    
    // Try connecting with port specification
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    // If connection fails, try without database name first to check if MySQL is accessible
    if ($conn->connect_error) {
        // Try connecting to MySQL server without database
        $testConn = @new mysqli(DB_HOST, DB_USER, DB_PASS, '', DB_PORT);
        
        if ($testConn->connect_error) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Database connection failed']);
                exit;
            }
            die("MySQL Connection Error: " . $testConn->connect_error . 
                "<br><br>Please check:<br>" .
                "1. MySQL service is running in XAMPP Control Panel<br>" .
                "2. MySQL is running on port " . DB_PORT . "<br>" .
                "3. No firewall is blocking the connection");
        }
        
        $testConn->close();
        
        // If MySQL is accessible but database doesn't exist
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database does not exist']);
            exit;
        }
        die("MySQL is running, but database '" . DB_NAME . "' does not exist.<br><br>" .
            "Please create the database by running the database.sql file in phpMyAdmin.");
    }
    
    return $conn;
}
?>
