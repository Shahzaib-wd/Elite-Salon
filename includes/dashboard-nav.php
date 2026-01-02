<?php
require_once __DIR__ . '/auth.php';
$current_page = basename($_SERVER['PHP_SELF']);
$role = get_user_role();
?>
<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/elite-salon/<?php echo $role; ?>/dashboard.php">
            <i class="bi bi-scissors"></i> Elite Salon
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#dashboardNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="dashboardNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="/elite-salon/<?php echo $role; ?>/dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                
                <?php if ($role === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'appointments.php' ? 'active' : ''; ?>" href="/elite-salon/admin/appointments.php">
                            <i class="bi bi-calendar-check"></i> Appointments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'staff.php' ? 'active' : ''; ?>" href="/elite-salon/admin/staff.php">
                            <i class="bi bi-people"></i> Staff
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'payments.php' ? 'active' : ''; ?>" href="/elite-salon/admin/payments.php">
                            <i class="bi bi-credit-card"></i> Payments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'inventory.php' ? 'active' : ''; ?>" href="/elite-salon/admin/inventory.php">
                            <i class="bi bi-box-seam"></i> Inventory
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'reports.php' ? 'active' : ''; ?>" href="/elite-salon/admin/reports.php">
                            <i class="bi bi-graph-up"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>" href="/elite-salon/admin/settings.php">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                    </li>
                <?php elseif ($role === 'stylist'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'appointments.php' ? 'active' : ''; ?>" href="/elite-salon/stylist/appointments.php">
                            <i class="bi bi-calendar-check"></i> My Appointments
                        </a>
                    </li>
                <?php elseif ($role === 'receptionist'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'appointments.php' ? 'active' : ''; ?>" href="/elite-salon/receptionist/appointments.php">
                            <i class="bi bi-calendar-check"></i> Appointments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'payments.php' ? 'active' : ''; ?>" href="/elite-salon/receptionist/payments.php">
                            <i class="bi bi-credit-card"></i> Payments
                        </a>
                    </li>
                <?php elseif ($role === 'user'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'appointments.php' ? 'active' : ''; ?>" href="/elite-salon/user/appointments.php">
                            <i class="bi bi-calendar-check"></i> My Appointments
                        </a>
                    </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>" href="/elite-salon/<?php echo $role; ?>/profile.php">
                        <i class="bi bi-person"></i> Profile
                    </a>
                </li>
            </ul>
            
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?php echo get_user_name(); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/elite-salon/<?php echo $role; ?>/profile.php">My Profile</a></li>
                        <li><a class="dropdown-item" href="/elite-salon/index.php">Home</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/elite-salon/logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
