<?php
$page_title = 'My Profile - Elite Salon';
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('receptionist');

$user_id = get_user_id();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' AND id != $user_id");
    if (mysqli_num_rows($check) > 0) {
        $error = 'Email already exists!';
    } else {
        mysqli_query($conn, "UPDATE users SET name = '$name', email = '$email', phone = '$phone' WHERE id = $user_id");
        
        if (!empty($_POST['new_password'])) {
            $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET password_hash = '$new_password' WHERE id = $user_id");
        }
        
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['success'] = 'Profile updated successfully!';
        header("Location: /elite-salon/stylist/profile.php");
        exit();
    }
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id"));

require_once '../includes/header.php';
require_once '../includes/dashboard-nav.php';
?>

<div class="dashboard-container">
    <div class="container-fluid">
        <div class="dashboard-header">
            <h1><i class="bi bi-person-circle"></i> My Profile</h1>
            <p>Manage your account information</p>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="content-card">
                    <h4><i class="bi bi-pencil"></i> Edit Profile</h4>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control" 
                                   value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" 
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5>Change Password</h5>
                        <p class="text-muted">Leave blank to keep current password</p>
                        
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" minlength="6">
                            <small class="text-muted">Minimum 6 characters</small>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="content-card">
                    <h4><i class="bi bi-info-circle"></i> Account Information</h4>
                    
                    <div class="mb-3">
                        <strong>User ID:</strong><br>
                        #<?php echo $user['id']; ?>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Role:</strong><br>
                        <span class="badge bg-primary"><?php echo ucfirst($user['role']); ?></span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        <span class="badge bg-success"><?php echo ucfirst($user['status']); ?></span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Member Since:</strong><br>
                        <?php echo date('F d, Y', strtotime($user['created_at'])); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
