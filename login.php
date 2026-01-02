<?php
$page_title = 'Login - Elite Salon';
require_once 'includes/auth.php';

// Redirect if already logged in
if (is_logged_in()) {
    header("Location: " . get_dashboard_url(get_user_role()));
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $result = login_user($email, $password);
        
        if ($result['success']) {
            header("Location: " . get_dashboard_url($result['role']));
            exit();
        } else {
            $error = $result['message'];
        }
    }
}

require_once 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="text-center mb-4">
            <i class="bi bi-scissors" style="font-size: 4rem; color: var(--primary-color);"></i>
        </div>
        <h2>Login to Elite Salon</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['registered']) && $_GET['registered'] === 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Registration successful! Please login with your credentials.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        
        <hr class="my-4">
        
        <div class="text-center">
            <p class="mb-2">Don't have an account? <a href="/elite-salon/register.php">Register here</a></p>
            <p class="mb-0"><a href="/elite-salon/index.php">← Back to Home</a></p>
        </div>
        
        <div class="mt-4 p-3" style="background-color: #f3f4f6; border-radius: 10px;">
            <h6 class="text-center mb-3">Demo Login Credentials</h6>
            <small class="d-block mb-1"><strong>Admin:</strong> admin@elitesalon.com / admin123</small>
            <small class="d-block mb-1"><strong>Stylist:</strong> stylist@elitesalon.com / stylist123</small>
            <small class="d-block mb-1"><strong>Receptionist:</strong> receptionist@elitesalon.com / receptionist123</small>
            <small class="d-block"><strong>User:</strong> user@elitesalon.com / user123</small>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
