<?php
$page_title = 'Reports - Elite Salon';
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('admin');

// Fetch report data
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'completed'"))['total'];
$monthly_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'completed' AND MONTH(payment_date) = MONTH(CURDATE()) AND YEAR(payment_date) = YEAR(CURDATE())"))['total'];
$total_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments"))['count'];
$completed_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE status = 'completed'"))['count'];
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'user'"))['count'];

// Top services
$top_services = mysqli_query($conn, "SELECT s.name, COUNT(a.id) as bookings, s.price 
                                     FROM appointments a 
                                     JOIN services s ON a.service_id = s.id 
                                     GROUP BY a.service_id 
                                     ORDER BY bookings DESC LIMIT 5");

// Revenue by month
$monthly_data = mysqli_query($conn, "SELECT MONTH(payment_date) as month, SUM(amount) as revenue 
                                     FROM payments 
                                     WHERE status = 'completed' AND YEAR(payment_date) = YEAR(CURDATE()) 
                                     GROUP BY MONTH(payment_date) 
                                     ORDER BY month");

require_once '../includes/header.php';
require_once '../includes/dashboard-nav.php';
?>

<div class="dashboard-container">
    <div class="container-fluid">
        <div class="dashboard-header">
            <h1><i class="bi bi-graph-up"></i> Reports & Analytics</h1>
            <p>View business performance and statistics</p>
        </div>
        
        <!-- Revenue Statistics -->
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="stats-card success">
                    <div class="icon"><i class="bi bi-cash-stack"></i></div>
                    <h3>$<?php echo number_format($total_revenue, 2); ?></h3>
                    <p>Total Revenue</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="stats-card primary">
                    <div class="icon"><i class="bi bi-calendar-month"></i></div>
                    <h3>$<?php echo number_format($monthly_revenue, 2); ?></h3>
                    <p>This Month's Revenue</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="stats-card warning">
                    <div class="icon"><i class="bi bi-calendar-check"></i></div>
                    <h3><?php echo $total_appointments; ?></h3>
                    <p>Total Appointments</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="stats-card danger">
                    <div class="icon"><i class="bi bi-people"></i></div>
                    <h3><?php echo $total_customers; ?></h3>
                    <p>Total Customers</p>
                </div>
            </div>
        </div>
        
        <!-- Top Services -->
        <div class="row mt-4">
            <div class="col-lg-6">
                <div class="content-card">
                    <h4><i class="bi bi-star-fill"></i> Top Services</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Service Name</th>
                                    <th>Bookings</th>
                                    <th>Price</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($top_services) > 0): ?>
                                    <?php while ($service = mysqli_fetch_assoc($top_services)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($service['name']); ?></td>
                                            <td><span class="badge bg-primary"><?php echo $service['bookings']; ?></span></td>
                                            <td>$<?php echo number_format($service['price'], 2); ?></td>
                                            <td><strong>$<?php echo number_format($service['bookings'] * $service['price'], 2); ?></strong></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No data available</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="content-card">
                    <h4><i class="bi bi-calendar-month"></i> Monthly Revenue (Current Year)</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($monthly_data) > 0): ?>
                                    <?php 
                                    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                    while ($data = mysqli_fetch_assoc($monthly_data)): 
                                    ?>
                                        <tr>
                                            <td><?php echo $months[$data['month'] - 1]; ?></td>
                                            <td><strong>$<?php echo number_format($data['revenue'], 2); ?></strong></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="text-center">No data available</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Appointment Statistics -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="content-card">
                    <h4><i class="bi bi-pie-chart-fill"></i> Appointment Statistics</h4>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="text-center p-3" style="background: #f3f4f6; border-radius: 10px;">
                                <h3 class="text-primary"><?php echo $total_appointments; ?></h3>
                                <p class="mb-0">Total</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3" style="background: #f3f4f6; border-radius: 10px;">
                                <h3 class="text-success"><?php echo $completed_appointments; ?></h3>
                                <p class="mb-0">Completed</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3" style="background: #f3f4f6; border-radius: 10px;">
                                <h3 class="text-warning"><?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE status = 'pending'"))['count']; ?></h3>
                                <p class="mb-0">Pending</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3" style="background: #f3f4f6; border-radius: 10px;">
                                <h3 class="text-danger"><?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments WHERE status = 'cancelled'"))['count']; ?></h3>
                                <p class="mb-0">Cancelled</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
