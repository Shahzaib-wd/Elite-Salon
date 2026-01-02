<?php
$page_title = 'Stylist Dashboard - Elite Salon';
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('stylist');

$user_id = get_user_id();

// Fetch statistics
$total_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE stylist_id = $user_id"))['count'];
$today_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE stylist_id = $user_id AND appointment_date = CURDATE()"))['count'];
$upcoming_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE stylist_id = $user_id AND appointment_date >= CURDATE() AND status != 'cancelled'"))['count'];
$completed_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE stylist_id = $user_id AND status = 'completed'"))['count'];

// Fetch today's appointments
$today_apt_query = "SELECT a.*, u.name as user_name, u.phone, srv.name as service_name 
                    FROM appointments a 
                    LEFT JOIN users u ON a.user_id = u.id 
                    LEFT JOIN services srv ON a.service_id = srv.id 
                    WHERE a.stylist_id = $user_id AND a.appointment_date = CURDATE() 
                    ORDER BY a.appointment_time";
$today_appointments_list = mysqli_query($conn, $today_apt_query);

require_once '../includes/header.php';
require_once '../includes/dashboard-nav.php';
?>

<div class="dashboard-container">
    <div class="container-fluid">
        <div class="dashboard-header">
            <h1><i class="bi bi-speedometer2"></i> Stylist Dashboard</h1>
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
                <div class="stats-card success">
                    <div class="icon"><i class="bi bi-check-circle-fill"></i></div>
                    <h3><?php echo $completed_appointments; ?></h3>
                    <p>Completed</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="stats-card danger">
                    <div class="icon"><i class="bi bi-clock-fill"></i></div>
                    <h3><?php echo $upcoming_appointments; ?></h3>
                    <p>Upcoming</p>
                </div>
            </div>
        </div>
        
        <!-- Today's Appointments -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="content-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                        <h4><i class="bi bi-calendar-day"></i> Today's Schedule</h4>
                        <a href="/elite-salon/stylist/appointments.php" class="btn btn-primary btn-sm">View All Appointments</a>
                    </div>
                    
                    <?php if (mysqli_num_rows($today_appointments_list) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Customer</th>
                                        <th>Service</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($apt = mysqli_fetch_assoc($today_appointments_list)): ?>
                                        <tr>
                                            <td><strong><?php echo date('h:i A', strtotime($apt['appointment_time'])); ?></strong></td>
                                            <td><?php echo htmlspecialchars($apt['user_name']); ?></td>
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
                                            <td><?php echo htmlspecialchars($apt['notes'] ?? '-'); ?></td>
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
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
