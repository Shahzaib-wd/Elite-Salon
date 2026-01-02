<?php
$page_title = 'My Dashboard - Elite Salon';
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('user');

$user_id = get_user_id();

// Fetch statistics
$total_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE user_id = $user_id"))['count'];
$upcoming_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE user_id = $user_id AND appointment_date >= CURDATE() AND status NOT IN ('completed', 'cancelled')"))['count'];
$completed_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE user_id = $user_id AND status = 'completed'"))['count'];
$total_spent = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE user_id = $user_id AND status = 'completed'"))['total'];

// Fetch upcoming appointments
$upcoming_apt_query = "SELECT a.*, s.name as stylist_name, srv.name as service_name, srv.price 
                       FROM appointments a 
                       LEFT JOIN users s ON a.stylist_id = s.id 
                       LEFT JOIN services srv ON a.service_id = srv.id 
                       WHERE a.user_id = $user_id AND a.appointment_date >= CURDATE() AND a.status NOT IN ('cancelled') 
                       ORDER BY a.appointment_date, a.appointment_time 
                       LIMIT 5";
$upcoming_appointments_list = mysqli_query($conn, $upcoming_apt_query);

// Fetch available services
$services = mysqli_query($conn, "SELECT * FROM services WHERE status = 'active' ORDER BY name LIMIT 4");

require_once '../includes/header.php';
require_once '../includes/dashboard-nav.php';
?>

<div class="dashboard-container">
    <div class="container-fluid">
        <div class="dashboard-header">
            <h1><i class="bi bi-speedometer2"></i> My Dashboard</h1>
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
                    <div class="icon"><i class="bi bi-clock-fill"></i></div>
                    <h3><?php echo $upcoming_appointments; ?></h3>
                    <p>Upcoming Appointments</p>
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
                    <div class="icon"><i class="bi bi-cash-stack"></i></div>
                    <h3>$<?php echo number_format($total_spent, 2); ?></h3>
                    <p>Total Spent</p>
                </div>
            </div>
        </div>
        
        <!-- Upcoming Appointments -->
        <div class="row mt-4">
            <div class="col-lg-8">
                <div class="content-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4><i class="bi bi-calendar-event"></i> Upcoming Appointments</h4>
                        <a href="/elite-salon/user/appointments.php" class="btn btn-primary btn-sm">View All</a>
                    </div>
                    
                    <?php if (mysqli_num_rows($upcoming_appointments_list) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Service</th>
                                        <th>Stylist</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($apt = mysqli_fetch_assoc($upcoming_appointments_list)): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo date('M d, Y', strtotime($apt['appointment_date'])); ?></strong><br>
                                                <small><?php echo date('h:i A', strtotime($apt['appointment_time'])); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($apt['service_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($apt['stylist_name'] ?? 'Not assigned'); ?></td>
                                            <td><strong>$<?php echo number_format($apt['price'] ?? 0, 2); ?></strong></td>
                                            <td>
                                                <?php
                                                $status_class = ['pending' => 'warning', 'confirmed' => 'primary', 'completed' => 'success'];
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
                            <i class="bi bi-info-circle"></i> You have no upcoming appointments.
                            <a href="/elite-salon/user/appointments.php" class="alert-link">Book one now!</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="content-card">
                    <h4><i class="bi bi-lightning-fill"></i> Quick Actions</h4>
                    <div class="d-grid gap-2 mt-3">
                        <a href="/elite-salon/user/appointments.php" class="btn btn-primary btn-lg">
                            <i class="bi bi-calendar-plus"></i> Book Appointment
                        </a>
                        <a href="/elite-salon/user/profile.php" class="btn btn-success btn-lg">
                            <i class="bi bi-person"></i> My Profile
                        </a>
                        <a href="/elite-salon/index.php" class="btn btn-secondary btn-lg">
                            <i class="bi bi-house"></i> Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Available Services -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="content-card">
                    <h4><i class="bi bi-star-fill"></i> Our Popular Services</h4>
                    <div class="row mt-3">
                        <?php while ($service = mysqli_fetch_assoc($services)): ?>
                            <div class="col-md-6 col-lg-3 mb-3">
                                <div class="service-card text-center">
                                    <i class="bi bi-scissors"></i>
                                    <h5><?php echo htmlspecialchars($service['name']); ?></h5>
                                    <p><?php echo htmlspecialchars(substr($service['description'], 0, 60)) . '...'; ?></p>
                                    <h4 class="text-primary">$<?php echo number_format($service['price'], 2); ?></h4>
                                    <small class="text-light"><?php echo $service['duration']; ?> minutes</small>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="/elite-salon/user/appointments.php" class="btn btn-primary">View All Services & Book</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
