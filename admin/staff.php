<?php
$page_title = 'Staff Management - Elite Salon';
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('admin');

// Handle staff operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        $name = sanitize_input($_POST['name']);
        $email = sanitize_input($_POST['email']);
        $phone = sanitize_input($_POST['phone']);
        $role = sanitize_input($_POST['role']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $query = "INSERT INTO users (name, email, password_hash, phone, role, status) VALUES (?, ?, ?, ?, ?, 'active')";
        execute_query($query, "sssss", [$name, $email, $password, $phone, $role]);
        
        $_SESSION['success'] = 'Staff member added successfully!';
        header("Location: /elite-salon/admin/staff.php");
        exit();
    } elseif ($_POST['action'] === 'update_status') {
        $user_id = (int)$_POST['user_id'];
        $status = sanitize_input($_POST['status']);
        
        $query = "UPDATE users SET status = ? WHERE id = ?";
        execute_query($query, "si", [$status, $user_id]);
        
        $_SESSION['success'] = 'Staff status updated successfully!';
        header("Location: /elite-salon/admin/staff.php");
        exit();
    } elseif ($_POST['action'] === 'delete') {
        $user_id = (int)$_POST['user_id'];
        
        $query = "DELETE FROM users WHERE id = ? AND role IN ('stylist', 'receptionist')";
        execute_query($query, "i", [$user_id]);
        
        $_SESSION['success'] = 'Staff member deleted successfully!';
        header("Location: /elite-salon/admin/staff.php");
        exit();
    }
}

// Fetch all staff members
$staff_query = "SELECT * FROM users WHERE role IN ('stylist', 'receptionist') ORDER BY created_at DESC";
$staff_members = mysqli_query($conn, $staff_query);

require_once '../includes/header.php';
require_once '../includes/dashboard-nav.php';
?>

<div class="dashboard-container">
    <div class="container-fluid">
        <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="bi bi-people"></i> Staff Management</h1>
                    <p>Manage stylists and receptionists</p>
                </div>
                <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                    <i class="bi bi-person-plus"></i> Add Staff Member
                </button>
            </div>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="content-card">
            <h4><i class="bi bi-list"></i> All Staff Members</h4>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($staff_members) > 0): ?>
                            <?php while ($staff = mysqli_fetch_assoc($staff_members)): ?>
                                <tr>
                                    <td>#<?php echo $staff['id']; ?></td>
                                    <td><?php echo htmlspecialchars($staff['name']); ?></td>
                                    <td><?php echo htmlspecialchars($staff['email']); ?></td>
                                    <td><?php echo htmlspecialchars($staff['phone'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $staff['role'] == 'stylist' ? 'primary' : 'info'; ?>">
                                            <?php echo ucfirst($staff['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $staff['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($staff['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($staff['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                                data-bs-target="#statusModal<?php echo $staff['id']; ?>">
                                            <i class="bi bi-toggle-on"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal<?php echo $staff['id']; ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        
                                        <!-- Status Modal -->
                                        <div class="modal fade" id="statusModal<?php echo $staff['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Update Status</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="action" value="update_status">
                                                            <input type="hidden" name="user_id" value="<?php echo $staff['id']; ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label">Status</label>
                                                                <select name="status" class="form-select" required>
                                                                    <option value="active" <?php echo $staff['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                                                    <option value="inactive" <?php echo $staff['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Update</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal<?php echo $staff['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="user_id" value="<?php echo $staff['id']; ?>">
                                                            <p>Are you sure you want to delete this staff member?</p>
                                                            <p><strong><?php echo htmlspecialchars($staff['name']); ?></strong></p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Delete</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No staff members found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Role *</label>
                        <select name="role" class="form-select" required>
                            <option value="">Select Role</option>
                            <option value="stylist">Stylist</option>
                            <option value="receptionist">Receptionist</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                        <small class="text-muted">Minimum 6 characters</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Staff Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
