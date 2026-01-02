<?php
$page_title = 'Receptionist Dashboard - Elite Salon';
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('receptionist');

// Fetch statistics
$total_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments"))['count'];
$today_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE appointment_date = CURDATE()"))['count'];
$pending_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE status = 'pending'"))['count'];
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'user'"))['count'];
$today_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE DATE(payment_date) = CURDATE() AND status = 'completed'"))['total'];

// Fetch today's appointments
$today_apt_query = "SELECT a.*, u.name as user_name, u.phone, s.name as stylist_name, srv.name as service_name 
                    FROM appointments a 
                    LEFT JOIN users u ON a.user_id = u.id 
                    LEFT JOIN users s ON a.stylist_id = s.id 
                    LEFT JOIN services srv ON a.service_id = srv.id 
                    WHERE a.appointment_date = CURDATE() 
                    ORDER BY a.appointment_time";
$today_appointments_list = mysqli_query($conn, $today_apt_query);

require_once '../includes/header.php';
require_once '../includes/dashboard-nav.php';
?>

<div class="dashboard-container">
    <div class="container-fluid">
        <div class="dashboard-header">
            <h1><i class="bi bi-speedometer2"></i> Receptionist Dashboard</h1>
            <p>Welcome back, <?php echo get_user_name(); ?>!</p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card primary">
                    <div class="icon"><i class="bi bi-calendar-check-fill"></i></div>
                    <h3><?php echo $total_appointments; ?></h3>
                    <p>Total Appointments</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stats-card warning">
                    <div class="icon"><i class="bi bi-calendar-event-fill"></i></div>
                    <h3><?php echo $today_appointments; ?></h3>
                    <p>Today's Appointments</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stats-card danger">
                    <div class="icon"><i class="bi bi-hourglass-split"></i></div>
                    <h3><?php echo $pending_appointments; ?></h3>
                    <p>Pending Appointments</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stats-card success">
                    <div class="icon"><i class="bi bi-cash-stack"></i></div>
                    <h3>$<?php echo number_format($today_revenue, 2); ?></h3>
                    <p>Today's Revenue</p>
                </div>
            </div>
        </div>
        
        <!-- Today's Appointments -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="content-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4><i class="bi bi-calendar-day"></i> Today's Appointments</h4>
                        <a href="/elite-salon/receptionist/appointments.php" class="btn btn-primary btn-sm">Manage All</a>
                    </div>
                    
                    <?php if (mysqli_num_rows($today_appointments_list) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Customer</th>
                                        <th>Stylist</th>
                                        <th>Service</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($apt = mysqli_fetch_assoc($today_appointments_list)): ?>
                                        <tr>
                                            <td><strong><?php echo date('h:i A', strtotime($apt['appointment_time'])); ?></strong></td>
                                            <td><?php echo htmlspecialchars($apt['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($apt['stylist_name'] ?? 'Not assigned'); ?></td>
                                            <td><?php echo htmlspecialchars($apt['service_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($apt['phone'] ?? 'N/A'); ?></td>
                                            <td>
                                                <?php
                                                $status_class = ['pending' => 'warning', 'confirmed' => 'primary', 'completed' => 'success', 'cancelled' => 'danger'];
                                                ?>
                                                <span class="badge bg-<?php echo $status_class[$apt['status']]; ?>">
                                                    <?php echo ucfirst($apt['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No appointments scheduled for today.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="content-card">
                    <h4><i class="bi bi-lightning-fill"></i> Quick Actions</h4>
                    <div class="row mt-3">
                        <div class="col-md-4 mb-3">
                            <a href="/elite-salon/receptionist/appointments.php" class="btn btn-primary w-100 p-3">
                                <i class="bi bi-calendar-plus"></i><br>Manage Appointments
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="/elite-salon/receptionist/payments.php" class="btn btn-success w-100 p-3">
                                <i class="bi bi-credit-card"></i><br>Manage Payments
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="/elite-salon/receptionist/profile.php" class="btn btn-warning w-100 p-3">
                                <i class="bi bi-person"></i><br>My Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
