<?php
$page_title = 'My Appointments - Elite Salon';
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('stylist');

$user_id = get_user_id();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $appointment_id = (int)$_POST['appointment_id'];
        $status = sanitize_input($_POST['status']);
        
        // Only allow updating own appointments
        $query = "UPDATE appointments SET status = ? WHERE id = ? AND stylist_id = ?";
        execute_query($query, "sii", [$status, $appointment_id, $user_id]);
        
        $_SESSION['success'] = 'Appointment status updated successfully!';
        header("Location: /elite-salon/stylist/appointments.php");
        exit();
    }
}

// Fetch all appointments for this stylist
$appointments_query = "SELECT a.*, u.name as user_name, u.email, u.phone, srv.name as service_name, srv.price 
                       FROM appointments a 
                       LEFT JOIN users u ON a.user_id = u.id 
                       LEFT JOIN services srv ON a.service_id = srv.id 
                       WHERE a.stylist_id = $user_id 
                       ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$appointments = mysqli_query($conn, $appointments_query);

// Store appointments in array for modal generation
$appointments_array = [];
while ($appointment = mysqli_fetch_assoc($appointments)) {
    $appointments_array[] = $appointment;
}

require_once '../includes/header.php';
require_once '../includes/dashboard-nav.php';
?>

<!-- CRITICAL: Global Alert Container (below navbar, above content) -->
<div id="global-alert-container">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</div>

<div class="dashboard-container">
    <div class="container-fluid">
        <div class="dashboard-header">
            <h1><i class="bi bi-calendar-check"></i> My Appointments</h1>
            <p>View and manage your scheduled appointments</p>
        </div>
        
        <div class="content-card">
            <h4><i class="bi bi-list"></i> All My Appointments</h4>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Service</th>
                            <th>Date & Time</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($appointments_array) > 0): ?>
                            <?php foreach ($appointments_array as $appointment): ?>
                                <tr>
                                    <td>#<?php echo $appointment['id']; ?></td>
                                    <td><?php echo htmlspecialchars($appointment['user_name']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($appointment['email']); ?><br>
                                        <small><?php echo htmlspecialchars($appointment['phone'] ?? 'N/A'); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($appointment['service_name'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?><br>
                                        <small><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></small>
                                    </td>
                                    <td>$<?php echo number_format($appointment['price'] ?? 0, 2); ?></td>
                                    <td>
                                        <?php
                                        $status_class = [
                                            'pending' => 'warning',
                                            'confirmed' => 'primary',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        ?>
                                        <span class="badge bg-<?php echo $status_class[$appointment['status']]; ?>">
                                            <?php echo ucfirst($appointment['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($appointment['notes'] ?? '-'); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#updateStatusModal<?php echo $appointment['id']; ?>">
                                            <i class="bi bi-pencil"></i> Update
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No appointments found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ================================================================
     MODALS SECTION - PLACED AT END OF BODY (OUTSIDE TABLE)
     CRITICAL: Modals MUST be at document root level for proper positioning
     ================================================================ -->

<!-- Update Status Modals (Generated for each appointment) -->
<?php foreach ($appointments_array as $appointment): ?>
<div class="modal fade" id="updateStatusModal<?php echo $appointment['id']; ?>" tabindex="-1" aria-labelledby="updateStatusModalLabel<?php echo $appointment['id']; ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel<?php echo $appointment['id']; ?>">Update Appointment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                    
                    <div class="mb-3">
                        <p><strong>Customer:</strong> <?php echo htmlspecialchars($appointment['user_name']); ?></p>
                        <p><strong>Service:</strong> <?php echo htmlspecialchars($appointment['service_name'] ?? 'N/A'); ?></p>
                        <p><strong>Date:</strong> <?php echo date('M d, Y h:i A', strtotime($appointment['appointment_date'] . ' ' . $appointment['appointment_time'])); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" <?php echo $appointment['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo $appointment['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="completed" <?php echo $appointment['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $appointment['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php require_once '../includes/footer.php'; ?>
