<?php
$page_title = 'Settings - Elite Salon';
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('admin');

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_settings') {
        $salon_name = sanitize_input($_POST['salon_name']);
        $salon_email = sanitize_input($_POST['salon_email']);
        $salon_phone = sanitize_input($_POST['salon_phone']);
        $salon_address = sanitize_input($_POST['salon_address']);
        $working_hours_start = sanitize_input($_POST['working_hours_start']);
        $working_hours_end = sanitize_input($_POST['working_hours_end']);
        $booking_advance_days = sanitize_input($_POST['booking_advance_days']);
        $cancellation_hours = sanitize_input($_POST['cancellation_hours']);
        
        $settings = [
            'salon_name' => $salon_name,
            'salon_email' => $salon_email,
            'salon_phone' => $salon_phone,
            'salon_address' => $salon_address,
            'working_hours_start' => $working_hours_start,
            'working_hours_end' => $working_hours_end,
            'booking_advance_days' => $booking_advance_days,
            'cancellation_hours' => $cancellation_hours
        ];
        
        foreach ($settings as $key => $value) {
            mysqli_query($conn, "UPDATE settings SET setting_value = '$value' WHERE setting_key = '$key'");
        }
        
        $_SESSION['success'] = 'Settings updated successfully!';
        header("Location: /elite-salon/admin/settings.php");
        exit();
    }
}

// Fetch current settings
$settings_result = mysqli_query($conn, "SELECT * FROM settings");
$settings = [];
while ($row = mysqli_fetch_assoc($settings_result)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

require_once '../includes/header.php';
require_once '../includes/dashboard-nav.php';
?>

<div class="dashboard-container">
    <div class="container-fluid">
        <div class="dashboard-header">
            <h1><i class="bi bi-gear"></i> System Settings</h1>
            <p>Configure salon settings and preferences</p>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="content-card">
            <h4><i class="bi bi-sliders"></i> General Settings</h4>
            
            <form method="POST">
                <input type="hidden" name="action" value="update_settings">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Salon Name *</label>
                        <input type="text" name="salon_name" class="form-control" 
                               value="<?php echo htmlspecialchars($settings['salon_name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Salon Email *</label>
                        <input type="email" name="salon_email" class="form-control" 
                               value="<?php echo htmlspecialchars($settings['salon_email'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Salon Phone *</label>
                        <input type="tel" name="salon_phone" class="form-control" 
                               value="<?php echo htmlspecialchars($settings['salon_phone'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Salon Address *</label>
                        <input type="text" name="salon_address" class="form-control" 
                               value="<?php echo htmlspecialchars($settings['salon_address'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Working Hours Start *</label>
                        <input type="time" name="working_hours_start" class="form-control" 
                               value="<?php echo htmlspecialchars($settings['working_hours_start'] ?? '09:00'); ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Working Hours End *</label>
                        <input type="time" name="working_hours_end" class="form-control" 
                               value="<?php echo htmlspecialchars($settings['working_hours_end'] ?? '20:00'); ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Booking Advance Days *</label>
                        <input type="number" name="booking_advance_days" class="form-control" 
                               value="<?php echo htmlspecialchars($settings['booking_advance_days'] ?? '30'); ?>" required>
                        <small class="text-muted">How many days in advance customers can book</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cancellation Hours *</label>
                        <input type="number" name="cancellation_hours" class="form-control" 
                               value="<?php echo htmlspecialchars($settings['cancellation_hours'] ?? '24'); ?>" required>
                        <small class="text-muted">Minimum hours before appointment to cancel</small>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
