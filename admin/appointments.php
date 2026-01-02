<?php
$page_title = 'Appointments Management - Elite Salon';
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('admin');

// Handle appointment status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $appointment_id = (int)$_POST['appointment_id'];
        $status = sanitize_input($_POST['status']);
        
        $query = "UPDATE appointments SET status = ? WHERE id = ?";
        execute_query($query, "si", [$status, $appointment_id]);
        
        $_SESSION['success'] = 'Appointment status updated successfully!';
        header("Location: /elite-salon/admin/appointments.php");
        exit();
    } elseif ($_POST['action'] === 'delete') {
        $appointment_id = (int)$_POST['appointment_id'];
        
        $query = "DELETE FROM appointments WHERE id = ?";
        execute_query($query, "i", [$appointment_id]);
        
        $_SESSION['success'] = 'Appointment deleted successfully!';
        header("Location: /elite-salon/admin/appointments.php");
        exit();
    } elseif ($_POST['action'] === 'create') {
        $user_id = (int)$_POST['user_id'];
        $stylist_id = (int)$_POST['stylist_id'];
        $service_id = (int)$_POST['service_id'];
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];
        $notes = sanitize_input($_POST['notes']);
        
        $query = "INSERT INTO appointments (user_id, stylist_id, service_id, appointment_date, appointment_time, status, notes) 
                  VALUES (?, ?, ?, ?, ?, 'confirmed', ?)";
        execute_query($query, "iiisss", [$user_id, $stylist_id, $service_id, $appointment_date, $appointment_time, $notes]);
        
        $_SESSION['success'] = 'Appointment created successfully!';
        header("Location: /elite-salon/admin/appointments.php");
        exit();
    }
}

// Fetch all appointments
$appointments_query = "SELECT a.*, u.name as user_name, u.email as user_email, u.phone as user_phone,
                       s.name as stylist_name, srv.name as service_name, srv.price 
                       FROM appointments a 
                       LEFT JOIN users u ON a.user_id = u.id 
                       LEFT JOIN users s ON a.stylist_id = s.id 
                       LEFT JOIN services srv ON a.service_id = srv.id 
                       ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$appointments = mysqli_query($conn, $appointments_query);

// Store appointments in array for modal generation
$appointments_array = [];
while ($appointment = mysqli_fetch_assoc($appointments)) {
    $appointments_array[] = $appointment;
}

// Fetch users for dropdown
$users = mysqli_query($conn, "SELECT id, name, email FROM users WHERE role = 'user' AND status = 'active'");

// Fetch stylists for dropdown
$stylists = mysqli_query($conn, "SELECT id, name FROM users WHERE role = 'stylist' AND status = 'active'");

// Fetch services for dropdown
$services = mysqli_query($conn, "SELECT id, name, price FROM services WHERE status = 'active'");

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
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1><i class="bi bi-calendar-check"></i> Appointments Management</h1>
                    <p>Manage all salon appointments</p>
                </div>
                <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
                    <i class="bi bi-plus-circle"></i> New Appointment
                </button>
            </div>
        </div>
        
        <div class="content-card">
            <h4><i class="bi bi-list"></i> All Appointments</h4>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Stylist</th>
                            <th>Service</th>
                            <th>Date & Time</th>
                            <th>Price</th>
                            <th>Status</th>
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
                                        <?php echo htmlspecialchars($appointment['user_email']); ?><br>
                                        <small><?php echo htmlspecialchars($appointment['user_phone'] ?? 'N/A'); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($appointment['stylist_name'] ?? 'Not assigned'); ?></td>
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
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#updateStatusModal<?php echo $appointment['id']; ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal<?php echo $appointment['id']; ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
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

<!-- Create Appointment Modal -->
<div class="modal fade" id="createAppointmentModal" tabindex="-1" aria-labelledby="createAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAppointmentModalLabel">Create New Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer *</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Select Customer</option>
                                <?php mysqli_data_seek($users, 0); ?>
                                <?php while ($user = mysqli_fetch_assoc($users)): ?>
                                    <option value="<?php echo $user['id']; ?>">
                                        <?php echo htmlspecialchars($user['name'] . ' (' . $user['email'] . ')'); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stylist *</label>
                            <select name="stylist_id" class="form-select" required>
                                <option value="">Select Stylist</option>
                                <?php mysqli_data_seek($stylists, 0); ?>
                                <?php while ($stylist = mysqli_fetch_assoc($stylists)): ?>
                                    <option value="<?php echo $stylist['id']; ?>">
                                        <?php echo htmlspecialchars($stylist['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Service *</label>
                            <select name="service_id" class="form-select" required>
                                <option value="">Select Service</option>
                                <?php mysqli_data_seek($services, 0); ?>
                                <?php while ($service = mysqli_fetch_assoc($services)): ?>
                                    <option value="<?php echo $service['id']; ?>">
                                        <?php echo htmlspecialchars($service['name'] . ' - $' . number_format($service['price'], 2)); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date *</label>
                            <input type="date" name="appointment_date" class="form-control" required 
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Time *</label>
                            <input type="time" name="appointment_time" class="form-control" required>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>

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

<!-- Delete Confirmation Modals (Generated for each appointment) -->
<?php foreach ($appointments_array as $appointment): ?>
<div class="modal fade" id="deleteModal<?php echo $appointment['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $appointment['id']; ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel<?php echo $appointment['id']; ?>">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                    
                    <p>Are you sure you want to delete this appointment?</p>
                    <div class="mt-3">
                        <p><strong>Customer:</strong> <?php echo htmlspecialchars($appointment['user_name']); ?></p>
                        <p><strong>Service:</strong> <?php echo htmlspecialchars($appointment['service_name'] ?? 'N/A'); ?></p>
                        <p><strong>Date:</strong> <?php echo date('M d, Y h:i A', strtotime($appointment['appointment_date'] . ' ' . $appointment['appointment_time'])); ?></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php require_once '../includes/footer.php'; ?>
