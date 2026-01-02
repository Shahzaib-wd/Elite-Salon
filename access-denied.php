<?php
$page_title = 'Access Denied - Elite Salon';
require_once 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card text-center">
        <i class="bi bi-shield-exclamation" style="font-size: 5rem; color: var(--danger-color);"></i>
        <h2 class="mt-3">Access Denied</h2>
        <p class="lead">You don't have permission to access this page.</p>
        <hr>
        <div class="mt-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo get_dashboard_url(get_user_role()); ?>" class="btn btn-primary">Go to Dashboard</a>
            <?php else: ?>
                <a href="/elite-salon/login.php" class="btn btn-primary">Login</a>
            <?php endif; ?>
            <a href="/elite-salon/index.php" class="btn btn-secondary">Go to Home</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
