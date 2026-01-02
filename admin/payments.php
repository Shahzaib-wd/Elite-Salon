<?php
$page_title = 'Payments Management - Elite Salon';
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('admin');

// Handle payment operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        $user_id = (int)$_POST['user_id'];
        $appointment_id = !empty($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : NULL;
        $amount = (float)$_POST['amount'];
        $payment_method = sanitize_input($_POST['payment_method']);
        $status = sanitize_input($_POST['status']);
        $notes = sanitize_input($_POST['notes']);
        
        $query = "INSERT INTO payments (user_id, appointment_id, amount, payment_method, status, notes) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iidsss", $user_id, $appointment_id, $amount, $payment_method, $status, $notes);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        $_SESSION['success'] = 'Payment record created successfully!';
        header("Location: /elite-salon/admin/payments.php");
        exit();
    } elseif ($_POST['action'] === 'update_status') {
        $payment_id = (int)$_POST['payment_id'];
        $status = sanitize_input($_POST['status']);
        
        $query = "UPDATE payments SET status = ? WHERE id = ?";
        execute_query($query, "si", [$status, $payment_id]);
        
        $_SESSION['success'] = 'Payment status updated successfully!';
        header("Location: /elite-salon/admin/payments.php");
        exit();
    }
}

// Fetch all payments
$payments_query = "SELECT p.*, u.name as user_name, u.email, a.appointment_date, a.appointment_time 
                   FROM payments p 
                   LEFT JOIN users u ON p.user_id = u.id 
                   LEFT JOIN appointments a ON p.appointment_id = a.id 
                   ORDER BY p.payment_date DESC";
$payments = mysqli_query($conn, $payments_query);

// Fetch statistics
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'completed'"))['total'];
$pending_payments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'pending'"))['total'];

// Fetch users and appointments for form
$users = mysqli_query($conn, "SELECT id, name FROM users WHERE role = 'user'");
$appointments = mysqli_query($conn, "SELECT id, appointment_date, appointment_time FROM appointments");

require_once '../includes/header.php';
require_once '../includes/dashboard-nav.php';
?>

<div class="dashboard-container">
    <div class="container-fluid">
        <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="bi bi-credit-card"></i> Payments Management</h1>
                    <p>Track and manage all payments</p>
                </div>
                <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                    <i class="bi bi-plus-circle"></i> Add Payment
                </button>
            </div>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="stats-card success">
                    <div class="icon"><i class="bi bi-cash-stack"></i></div>
                    <h3>$<?php echo number_format($total_revenue, 2); ?></h3>
                    <p>Total Revenue (Completed)</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stats-card warning">
                    <div class="icon"><i class="bi bi-hourglass-split"></i></div>
                    <h3>$<?php echo number_format($pending_payments, 2); ?></h3>
                    <p>Pending Payments</p>
                </div>
            </div>
        </div>
        
        <div class="content-card">
            <h4><i class="bi bi-list"></i> All Payments</h4>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Appointment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($payments) > 0): ?>
                            <?php while ($payment = mysqli_fetch_assoc($payments)): ?>
                                <tr>
                                    <td>#<?php echo $payment['id']; ?></td>
                                    <td><?php echo htmlspecialchars($payment['user_name']); ?></td>
                                    <td><strong>$<?php echo number_format($payment['amount'], 2); ?></strong></td>
                                    <td><span class="badge bg-info"><?php echo ucfirst($payment['payment_method']); ?></span></td>
                                    <td>
                                        <?php
                                        $status_class = ['pending' => 'warning', 'completed' => 'success', 'refunded' => 'info', 'failed' => 'danger'];
                                        ?>
                                        <span class="badge bg-<?php echo $status_class[$payment['status']]; ?>">
                                            <?php echo ucfirst($payment['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y h:i A', strtotime($payment['payment_date'])); ?></td>
                                    <td>
                                        <?php if ($payment['appointment_date']): ?>
                                            <?php echo date('M d, Y', strtotime($payment['appointment_date'])); ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                                data-bs-target="#statusModal<?php echo $payment['id']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        
                                        <!-- Status Modal -->
                                        <div class="modal fade" id="statusModal<?php echo $payment['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Update Payment Status</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="action" value="update_status">
                                                            <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label">Status</label>
                                                                <select name="status" class="form-select" required>
                                                                    <option value="pending" <?php echo $payment['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                                    <option value="completed" <?php echo $payment['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                                    <option value="refunded" <?php echo $payment['status'] == 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                                                                    <option value="failed" <?php echo $payment['status'] == 'failed' ? 'selected' : ''; ?>>Failed</option>
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
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No payments found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label class="form-label">Customer *</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Select Customer</option>
                            <?php mysqli_data_seek($users, 0); ?>
                            <?php while ($user = mysqli_fetch_assoc($users)): ?>
                                <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Appointment (Optional)</label>
                        <select name="appointment_id" class="form-select">
                            <option value="">None</option>
                            <?php mysqli_data_seek($appointments, 0); ?>
                            <?php while ($apt = mysqli_fetch_assoc($appointments)): ?>
                                <option value="<?php echo $apt['id']; ?>">
                                    #<?php echo $apt['id']; ?> - <?php echo date('M d, Y', strtotime($apt['appointment_date'])); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Amount *</label>
                        <input type="number" name="amount" class="form-control" step="0.01" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Payment Method *</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="online">Online</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-select" required>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
