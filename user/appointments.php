<?php
$page_title = 'My Appointments - Elite Salon';
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('user');

$user_id = get_user_id();

// Handle appointment booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'book') {
        $stylist_id = (int)$_POST['stylist_id'];
        $service_id = (int)$_POST['service_id'];
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];
        $notes = sanitize_input($_POST['notes']);
        
        $query = "INSERT INTO appointments (user_id, stylist_id, service_id, appointment_date, appointment_time, status, notes) 
                  VALUES (?, ?, ?, ?, ?, 'pending', ?)";
        execute_query($query, "iiisss", [$user_id, $stylist_id, $service_id, $appointment_date, $appointment_time, $notes]);
        
        $_SESSION['success'] = 'Appointment booked successfully! Our team will confirm it soon.';
        header("Location: /elite-salon/user/appointments.php");
        exit();
    } elseif ($_POST['action'] === 'cancel') {
        $appointment_id = (int)$_POST['appointment_id'];
        
        // Only allow cancelling own appointments
        $query = "UPDATE appointments SET status = 'cancelled' WHERE id = ? AND user_id = ?";
        execute_query($query, "ii", [$appointment_id, $user_id]);
        
        $_SESSION['success'] = 'Appointment cancelled successfully!';
        header("Location: /elite-salon/user/appointments.php");
        exit();
    }
}

// Fetch user's appointments
$appointments_query = "SELECT a.*, s.name as stylist_name, srv.name as service_name, srv.price, srv.duration 
                       FROM appointments a 
                       LEFT JOIN users s ON a.stylist_id = s.id 
                       LEFT JOIN services srv ON a.service_id = srv.id 
                       WHERE a.user_id = $user_id 
                       ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$appointments = mysqli_query($conn, $appointments_query);

// Store appointments in array for modal generation
$appointments_array = [];
while ($appointment = mysqli_fetch_assoc($appointments)) {
    $appointments_array[] = $appointment;
}

// Fetch stylists for booking
$stylists = mysqli_query($conn, "SELECT id, name FROM users WHERE role = 'stylist' AND status = 'active'");

// Fetch services for booking
$services = mysqli_query($conn, "SELECT * FROM services WHERE status = 'active' ORDER BY name");

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
                    <h1><i class="bi bi-calendar-check"></i> My Appointments</h1>
                    <p>View and manage your appointments</p>
                </div>
                <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#bookAppointmentModal">
                    <i class="bi bi-plus-circle"></i> Book New Appointment
                </button>
            </div>
        </div>
        
        <div class="content-card">
            <h4><i class="bi bi-list"></i> All My Appointments</h4>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date & Time</th>
                            <th>Service</th>
                            <th>Stylist</th>
                            <th>Duration</th>
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
                                    <td>
                                        <?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?><br>
                                        <small><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($appointment['service_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['stylist_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo $appointment['duration'] ?? 'N/A'; ?> min</td>
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
                                        <?php if ($appointment['status'] == 'pending' || $appointment['status'] == 'confirmed'): ?>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#cancelModal<?php echo $appointment['id']; ?>">
                                                <i class="bi bi-x-circle"></i> Cancel
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted">No actions</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No appointments found. Book your first appointment!</td>
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

<!-- Book Appointment Modal -->
<div class="modal fade" id="bookAppointmentModal" tabindex="-1" aria-labelledby="bookAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookAppointmentModalLabel">Book New Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="book">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Service *</label>
                            <select name="service_id" class="form-select" required>
                                <option value="">Select Service</option>
                                <?php mysqli_data_seek($services, 0); ?>
                                <?php while ($service = mysqli_fetch_assoc($services)): ?>
                                    <option value="<?php echo $service['id']; ?>">
                                        <?php echo htmlspecialchars($service['name'] . ' - $' . number_format($service['price'], 2) . ' (' . $service['duration'] . ' min)'); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Preferred Stylist *</label>
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
                            <label class="form-label">Preferred Date *</label>
                            <input type="date" name="appointment_date" class="form-control" required 
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Preferred Time *</label>
                            <input type="time" name="appointment_time" class="form-control" required>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Special Requests / Notes</label>
                            <textarea name="notes" class="form-control" rows="3" 
                                      placeholder="Any special requirements or preferences..."></textarea>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> Your appointment will be marked as "Pending" and our team will confirm it shortly.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Book Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Appointment Modals (Generated for each cancellable appointment) -->
<?php foreach ($appointments_array as $appointment): ?>
    <?php if ($appointment['status'] == 'pending' || $appointment['status'] == 'confirmed'): ?>
<div class="modal fade" id="cancelModal<?php echo $appointment['id']; ?>" tabindex="-1" aria-labelledby="cancelModalLabel<?php echo $appointment['id']; ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="cancelModalLabel<?php echo $appointment['id']; ?>">Cancel Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="cancel">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> Are you sure you want to cancel this appointment?
                    </div>
                    
                    <div class="mt-3">
                        <p><strong>Service:</strong> <?php echo htmlspecialchars($appointment['service_name'] ?? 'N/A'); ?></p>
                        <p><strong>Stylist:</strong> <?php echo htmlspecialchars($appointment['stylist_name'] ?? 'N/A'); ?></p>
                        <p><strong>Date:</strong> <?php echo date('M d, Y h:i A', strtotime($appointment['appointment_date'] . ' ' . $appointment['appointment_time'])); ?></p>
                        <p><strong>Price:</strong> $<?php echo number_format($appointment['price'] ?? 0, 2); ?></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Appointment</button>
                    <button type="submit" class="btn btn-danger">Cancel Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>
    <?php endif; ?>
<?php endforeach; ?>

<?php require_once '../includes/footer.php'; ?>
