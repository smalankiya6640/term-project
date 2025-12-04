<?php
// Database setup script
require_once 'config/database.php';

// Read and execute SQL file
$sql = file_get_contents('database.sql');

// Create database connection (without database name first)
$conn = @new mysqli('127.0.0.1', 'root', '', '', 3306);

if ($conn->connect_error) {
    die("MySQL Connection Error: " . $conn->connect_error . "\n");
}

// Create database first
$conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
$conn->select_db(DB_NAME);

// Remove CREATE DATABASE and USE statements as we've already handled them
$sql = preg_replace('/CREATE DATABASE IF NOT EXISTS.*?;/i', '', $sql);
$sql = preg_replace('/USE.*?;/i', '', $sql);

// Remove SQL comments (both -- style and /* */ style)
$sql = preg_replace('/--.*$/m', '', $sql); // Remove single-line comments
$sql = preg_replace('/\/\*.*?\*\//s', '', $sql); // Remove multi-line comments
$sql = preg_replace('/^\s*$/m', '', $sql); // Remove empty lines

// Execute all statements using multi_query
if ($conn->multi_query($sql)) {
    do {
        // Store first result set
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
}

// Check for errors (ignore duplicate key and table exists errors)
if ($conn->errno && $conn->errno != 1050 && $conn->errno != 1062) {
    echo "Warning: " . $conn->error . "\n";
} else {
    echo "Database setup complete!\n";
    echo "Database '" . DB_NAME . "' is ready to use.\n";
}

$conn->close();
?>

