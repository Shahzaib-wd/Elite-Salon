<?php
/**
 * Database Configuration and Connection
 * Elite Salon Management System
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'elite_salon');

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8mb4");

// Function to sanitize input
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

// Function to execute prepared statements
function execute_query($query, $types = "", $params = []) {
    global $conn;
    
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt === false) {
        return false;
    }
    
    if (!empty($types) && !empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    $result = mysqli_stmt_execute($stmt);
    
    if ($result === false) {
        mysqli_stmt_close($stmt);
        return false;
    }
    
    $return_value = mysqli_stmt_get_result($stmt);
    
    if ($return_value === false) {
        $return_value = $result;
    }
    
    mysqli_stmt_close($stmt);
    
    return $return_value;
}
?>
