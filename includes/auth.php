<?php
/**
 * Authentication and Authorization Functions
 * Elite Salon Management System
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

/**
 * Check if user has specific role
 */
function has_role($required_role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $required_role;
}

/**
 * Require login - redirect to login if not authenticated
 */
function require_login($login_page = '/elite-salon/login.php') {
    if (!is_logged_in()) {
        header("Location: " . $login_page);
        exit();
    }
}

/**
 * Require specific role - redirect if user doesn't have required role
 */
function require_role($required_role, $redirect_page = '/elite-salon/access-denied.php') {
    require_login();
    
    if (!has_role($required_role)) {
        header("Location: " . $redirect_page);
        exit();
    }
}

/**
 * Get current user ID
 */
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 */
function get_user_role() {
    return $_SESSION['role'] ?? null;
}

/**
 * Get current user name
 */
function get_user_name() {
    return $_SESSION['user_name'] ?? 'Guest';
}

/**
 * Login user
 */
function login_user($email, $password) {
    global $conn;
    
    $email = sanitize_input($email);
    
    $query = "SELECT id, name, email, password_hash, role, status FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password_hash'])) {
            if ($row['status'] === 'active') {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['role'] = $row['role'];
                
                mysqli_stmt_close($stmt);
                return ['success' => true, 'role' => $row['role']];
            } else {
                mysqli_stmt_close($stmt);
                return ['success' => false, 'message' => 'Account is inactive. Please contact administrator.'];
            }
        }
    }
    
    mysqli_stmt_close($stmt);
    return ['success' => false, 'message' => 'Invalid email or password.'];
}

/**
 * Register new user
 */
function register_user($name, $email, $password, $phone = '', $role = 'user') {
    global $conn;
    
    $name = sanitize_input($name);
    $email = sanitize_input($email);
    $phone = sanitize_input($phone);
    
    // Check if email already exists
    $check_query = "SELECT id FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Email already exists.'];
    }
    mysqli_stmt_close($stmt);
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $insert_query = "INSERT INTO users (name, email, password_hash, phone, role, status, created_at) VALUES (?, ?, ?, ?, ?, 'active', NOW())";
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $password_hash, $phone, $role);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => true, 'message' => 'Registration successful!'];
    }
    
    mysqli_stmt_close($stmt);
    return ['success' => false, 'message' => 'Registration failed. Please try again.'];
}

/**
 * Logout user
 */
function logout_user() {
    session_unset();
    session_destroy();
    header("Location: /elite-salon/index.php");
    exit();
}

/**
 * Get dashboard URL based on role
 */
function get_dashboard_url($role) {
    $dashboards = [
        'admin' => '/elite-salon/admin/dashboard.php',
        'stylist' => '/elite-salon/stylist/dashboard.php',
        'receptionist' => '/elite-salon/receptionist/dashboard.php',
        'user' => '/elite-salon/user/dashboard.php'
    ];
    
    return $dashboards[$role] ?? '/elite-salon/index.php';
}
?>
