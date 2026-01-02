<?php
$page_title = 'Admin Dashboard - Elite Salon';
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Check if user is admin
require_role('admin');

// Fetch dashboard statistics
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'user'"))['count'];
$total_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments"))['count'];
$today_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE appointment_date = CURDATE()"))['count'];
$pending_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE status = 'pending'"))['count'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'completed'"))['total'];
$today_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE DATE(payment_date) = CURDATE() AND status = 'completed'"))['total'];
$total_staff = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role IN ('stylist', 'receptionist')"))['count'];

// Fetch recent appointments
$recent_appointments_query = "SELECT a.*, u.name as user_name, s.name as stylist_name, srv.name as service_name 
                               FROM appointments a 
                               LEFT JOIN users u ON a.user_id = u.id 
                               LEFT JOIN users s ON a.stylist_id = s.id 
                               LEFT JOIN services srv ON a.service_id = srv.id 
                               ORDER BY a.created_at DESC LIMIT 5";
$recent_appointments = mysqli_query($conn, $recent_appointments_query);

require_once '../includes/header.php';
require_once '../includes/dashboard-nav.php';
?>

<div class="dashboard-container">
    <div class="container-fluid">
        <div class="dashboard-header">
            <h1><i class="bi bi-speedometer2"></i> Admin Dashboard</h1>
            <p>Welcome back, <?php echo get_user_name(); ?>!</p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card primary">
                    <div class="icon"><i class="bi bi-people-fill"></i></div>
                    <h3><?php echo $total_users; ?></h3>
                    <p>Total Customers</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stats-card success">
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
        </div>
        
        <div class="row mt-4">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                    <div class="icon"><i class="bi bi-cash-stack"></i></div>
                    <h3>$<?php echo number_format($total_revenue, 2); ?></h3>
                    <p>Total Revenue</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stats-card" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
                    <div class="icon"><i class="bi bi-credit-card-fill"></i></div>
                    <h3>$<?php echo number_format($today_revenue, 2); ?></h3>
                    <p>Today's Revenue</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stats-card" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); color: white;">
                    <div class="icon"><i class="bi bi-person-badge-fill"></i></div>
                    <h3><?php echo $total_staff; ?></h3>
                    <p>Staff Members</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stats-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                    <div class="icon"><i class="bi bi-box-seam-fill"></i></div>
                    <h3><?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM inventory WHERE status = 'low_stock'"))['count']; ?></h3>
                    <p>Low Stock Items</p>
                </div>
            </div>
        </div>
        
        <!-- Recent Appointments -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="content-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4><i class="bi bi-calendar-check"></i> Recent Appointments</h4>
                        <a href="/elite-salon/admin/appointments.php" class="btn btn-primary btn-sm">View All</a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Stylist</th>
                                    <th>Service</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($recent_appointments) > 0): ?>
                                    <?php while ($appointment = mysqli_fetch_assoc($recent_appointments)): ?>
                                        <tr>
                                            <td>#<?php echo $appointment['id']; ?></td>
                                            <td><?php echo htmlspecialchars($appointment['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['stylist_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['service_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo date('M d, Y h:i A', strtotime($appointment['appointment_date'] . ' ' . $appointment['appointment_time'])); ?></td>
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
                                                <a href="/elite-salon/admin/appointments.php" class="btn btn-sm btn-primary">View</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No appointments found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="content-card">
                    <h4><i class="bi bi-lightning-fill"></i> Quick Actions</h4>
                    <div class="row mt-3">
                        <div class="col-md-3 mb-3">
                            <a href="/elite-salon/admin/appointments.php" class="btn btn-primary w-100 p-3">
                                <i class="bi bi-calendar-plus"></i><br>Manage Appointments
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="/elite-salon/admin/staff.php" class="btn btn-success w-100 p-3">
                                <i class="bi bi-person-plus"></i><br>Manage Staff
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="/elite-salon/admin/payments.php" class="btn btn-warning w-100 p-3">
                                <i class="bi bi-credit-card"></i><br>View Payments
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="/elite-salon/admin/reports.php" class="btn btn-danger w-100 p-3">
                                <i class="bi bi-graph-up"></i><br>View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
