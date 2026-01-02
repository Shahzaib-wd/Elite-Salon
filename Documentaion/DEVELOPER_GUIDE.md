# 🛠️ ELITE SALON - DEVELOPER GUIDE

## Technical Documentation for Developers

Complete technical reference for Elite Salon's luxury black, gold, and silver themed salon management system.

---

## 📋 Table of Contents

1. [Technology Stack](#technology-stack)
2. [Project Structure](#project-structure)
3. [Color System & Design Theory](#color-system--design-theory)
4. [Typography System](#typography-system)
5. [Bootstrap Override Strategy](#bootstrap-override-strategy)
6. [Database Schema](#database-schema)
7. [Authentication System](#authentication-system)
8. [Booking System Logic](#booking-system-logic)
9. [JavaScript Architecture](#javascript-architecture)
10. [Setup & Deployment](#setup--deployment)
11. [Customization Guide](#customization-guide)
12. [Security Best Practices](#security-best-practices)
13. [Performance Optimization](#performance-optimization)
14. [Troubleshooting](#troubleshooting)

---

## 💻 Technology Stack

### Frontend
- **HTML5**: Semantic markup
- **CSS3**: Custom luxury theme + Bootstrap overrides
- **JavaScript (ES6+)**: Vanilla JS + optional jQuery
- **Bootstrap 5.3.2**: Grid system and base components
- **Bootstrap Icons 1.11.1**: Icon library
- **Google Fonts**: Playfair Display + Inter

### Backend
- **PHP 7.4+**: Server-side logic
- **MySQL 5.7+**: Database management
- **Sessions**: PHP native session handling

### Additional Tools
- **Git**: Version control
- **Apache/Nginx**: Web server
- **.htaccess**: URL rewriting and security

---

## 📁 Project Structure

```
elite-salon/
│
├── assets/
│   ├── css/
│   │   ├── styles.css          # Original Bootstrap theme (deprecated)
│   │   └── luxury-theme.css    # NEW: Ultra-premium luxury overrides
│   │
│   └── js/
│       └── main.js              # JavaScript functionality
│
├── includes/
│   ├── auth.php                 # Authentication functions
│   ├── db.php                   # Database connection
│   ├── header.php               # HTML head + CSS loading
│   ├── navbar.php               # Navigation bar
│   ├── footer.php               # Footer scripts
│   └── dashboard-nav.php        # Dashboard navigation
│
├── admin/
│   ├── dashboard.php            # Admin dashboard
│   ├── appointments.php         # Manage all appointments
│   ├── staff.php                # Manage staff
│   ├── payments.php             # Payment management
│   ├── inventory.php            # Inventory tracking
│   ├── reports.php              # Analytics & reports
│   ├── settings.php             # System settings
│   └── profile.php              # Admin profile
│
├── stylist/
│   ├── dashboard.php            # Stylist dashboard
│   ├── appointments.php         # View assigned appointments
│   └── profile.php              # Stylist profile
│
├── receptionist/
│   ├── dashboard.php            # Receptionist dashboard
│   ├── appointments.php         # Create/manage appointments
│   ├── payments.php             # Process payments
│   └── profile.php              # Receptionist profile
│
├── user/
│   ├── dashboard.php            # User dashboard
│   ├── appointments.php         # User bookings
│   └── profile.php              # User profile
│
├── index.php                    # Homepage (public)
├── login.php                    # Login page
├── register.php                 # Registration page
├── logout.php                   # Logout handler
├── access-denied.php            # Permission error page
├── check.php                    # System health check
├── database.sql                 # Database schema
├── .htaccess                    # Apache configuration
├── README.md                    # Project README
├── USER_GUIDE.md                # User documentation (NEW)
├── DEVELOPER_GUIDE.md           # This file (NEW)
└── VISUAL_CHANGELOG.md          # Change history
```

---

## 🎨 Color System & Design Theory

### Luxury Color Palette

```css
:root {
    /* Primary Colors */
    --luxury-black: #0a0a0a;           /* Primary background */
    --luxury-charcoal: #121212;        /* Surfaces, cards */
    --luxury-gold: #d4af37;            /* Accent, CTAs */
    --luxury-gold-glow: rgba(212, 175, 55, 0.35);  /* Hover effects */
    --luxury-silver: #cfd2d6;          /* Primary text */
    --luxury-silver-muted: #9ca3af;    /* Secondary text */
    --luxury-border: rgba(212, 175, 55, 0.25);     /* Borders */
}
```

### Color Psychology

#### **Black (#0a0a0a)**
- **Purpose**: Primary background, creates luxury foundation
- **Usage**: Body background, hero overlays, navbar
- **Psychology**: Sophistication, elegance, exclusivity, premium quality
- **Rule**: Never use bright white - always silver or gold text on black

#### **Charcoal (#121212)**
- **Purpose**: Elevated surfaces, cards, panels
- **Usage**: Service cards, modals, form containers, dashboard cards
- **Psychology**: Depth, layering, modern minimalism
- **Rule**: Provides subtle contrast against pure black

#### **Luxury Gold (#d4af37)**
- **Purpose**: Accent color, CTAs, highlights
- **Usage**: Buttons, borders, icons, hover states, dividers
- **Psychology**: Wealth, luxury, exclusivity, premium service
- **Rule**: Use sparingly - less is more. Never backgrounds, only accents

#### **Silver (#cfd2d6)**
- **Purpose**: Primary readable text
- **Usage**: Body text, headings, labels
- **Psychology**: Modernity, professionalism, clarity
- **Rule**: High contrast against black backgrounds for readability

#### **Muted Silver (#9ca3af)**
- **Purpose**: Secondary text, less important information
- **Usage**: Subtitles, descriptions, meta information
- **Psychology**: Hierarchy, supporting information
- **Rule**: Use for secondary content that shouldn't dominate

### Design Principles

1. **Minimalism**: Less is more - avoid clutter
2. **Hierarchy**: Use size, weight, and color to guide attention
3. **Contrast**: High contrast for readability (silver on black)
4. **Breathing Space**: Generous padding and margins
5. **Gold as Accent**: Never overuse - strategic placement only
6. **No Bright Colors**: Avoid blue, pink, purple, or bright gradients
7. **Premium Feel**: Every element should feel expensive and intentional

---

## 🔤 Typography System

### Font Families

```css
/* Headings - Elegant Serif */
h1, h2, h3, h4, h5, h6 {
    font-family: 'Playfair Display', Georgia, serif !important;
}

/* Body - Clean Sans-Serif */
body, p, li, span {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
}
```

### Font Loading (header.php)

```html
<!-- Google Fonts - Luxury Typography -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
```

### Typography Hierarchy

```css
h1 { font-size: clamp(2.5rem, 5vw, 4rem); }      /* Hero titles */
h2 { font-size: clamp(2rem, 4vw, 3rem); }        /* Section headings */
h3 { font-size: clamp(1.5rem, 3vw, 2rem); }      /* Subsections */
h4 { font-size: 1.5rem; }                        /* Card titles */
h5 { font-size: 1.25rem; }                       /* Small headings */
h6 { font-size: 1rem; }                          /* Smallest headings */

body { font-size: 1rem; line-height: 1.7; }      /* Body text */
```

### Typography Best Practices

- **Playfair Display**: Use for all headings (h1-h6)
  - Elegant serif font
  - Conveys luxury and sophistication
  - High contrast strokes
  
- **Inter**: Use for body text, buttons, labels
  - Highly readable sans-serif
  - Modern and clean
  - Excellent for UI elements

- **Letter Spacing**: Increase for headings and buttons (0.05em - 0.15em)
- **Text Transform**: Uppercase for buttons and small headings
- **Line Height**: 1.7 for body text, 1.1-1.2 for headings

---

## 🛡️ Bootstrap Override Strategy

### CRITICAL: CSS Load Order

**❌ WRONG ORDER (Bootstrap overwrites custom styles):**
```html
<link rel="stylesheet" href="custom-theme.css">
<link href="bootstrap.min.css" rel="stylesheet">
```

**✅ CORRECT ORDER (Custom styles override Bootstrap):**
```html
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display...&display=swap" rel="stylesheet">

<!-- CRITICAL: Luxury Theme MUST load AFTER Bootstrap -->
<link rel="stylesheet" href="/elite-salon/assets/css/luxury-theme.css">
```

### Bootstrap CSS Variable Overrides

```css
:root {
    /* Override Bootstrap variables */
    --bs-body-bg: #0a0a0a !important;
    --bs-body-color: #cfd2d6 !important;
    --bs-primary: #d4af37 !important;
    --bs-primary-rgb: 212, 175, 55 !important;
    --bs-secondary: #9ca3af !important;
    --bs-dark: #0a0a0a !important;
    --bs-light: #121212 !important;
    --bs-border-color: rgba(212, 175, 55, 0.25) !important;
    --bs-link-color: #d4af37 !important;
}
```

### Component-Specific Overrides

#### **Buttons**
```css
.btn {
    background-color: var(--luxury-black) !important;
    color: var(--luxury-gold) !important;
    border: 2px solid var(--luxury-gold) !important;
    border-radius: 0 !important;  /* Remove Bootstrap rounding */
    text-transform: uppercase !important;
    letter-spacing: 1.5px !important;
}

.btn:hover {
    background-color: var(--luxury-gold) !important;
    color: var(--luxury-black) !important;
    box-shadow: 0 0 30px var(--luxury-gold-glow) !important;
}
```

#### **Cards**
```css
.card {
    background-color: var(--luxury-charcoal) !important;
    border: 1px solid var(--luxury-border) !important;
    border-radius: 0 !important;  /* Remove Bootstrap rounding */
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6) !important;
}
```

#### **Forms**
```css
.form-control {
    background-color: var(--luxury-charcoal) !important;
    border: 1px solid var(--luxury-border) !important;
    border-radius: 0 !important;
    color: var(--luxury-silver) !important;
}

.form-control:focus {
    border-color: var(--luxury-gold) !important;
    box-shadow: 0 0 0 0.2rem var(--luxury-gold-glow) !important;
}
```

#### **Navbar**
```css
.navbar {
    background-color: rgba(10, 10, 10, 0.95) !important;
    backdrop-filter: blur(20px) !important;
    border-bottom: 1px solid var(--luxury-border) !important;
}

.nav-link {
    color: var(--luxury-silver) !important;
}

.nav-link:hover {
    color: var(--luxury-gold) !important;
}
```

### Why Use !important?

Bootstrap's specificity is very high. Using `!important` ensures our luxury theme always takes precedence:

```css
/* Without !important - Bootstrap wins */
.btn-primary {
    background-color: var(--luxury-gold);
}

/* With !important - Our theme wins */
.btn-primary {
    background-color: var(--luxury-gold) !important;
}
```

---

## 🗄️ Database Schema

### Tables Overview

```sql
-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'stylist', 'receptionist', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Appointments table
CREATE TABLE appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    stylist_id INT NOT NULL,
    service VARCHAR(255) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (stylist_id) REFERENCES users(id)
);

-- Payments table
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    status ENUM('pending', 'completed', 'refunded') DEFAULT 'pending',
    paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id)
);

-- Inventory table (optional)
CREATE TABLE inventory (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_name VARCHAR(255) NOT NULL,
    quantity INT DEFAULT 0,
    price DECIMAL(10,2),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Database Connection (includes/db.php)

```php
<?php
$host = 'localhost';
$dbname = 'elite_salon';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

---

## 🔐 Authentication System

### Session Management (includes/auth.php)

```php
<?php
session_start();

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function get_user_role() {
    return $_SESSION['role'] ?? 'user';
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: /elite-salon/login.php');
        exit;
    }
}

function require_role($allowed_roles) {
    require_login();
    if (!in_array(get_user_role(), $allowed_roles)) {
        header('Location: /elite-salon/access-denied.php');
        exit;
    }
}

function get_dashboard_url($role) {
    $dashboards = [
        'admin' => '/elite-salon/admin/dashboard.php',
        'stylist' => '/elite-salon/stylist/dashboard.php',
        'receptionist' => '/elite-salon/receptionist/dashboard.php',
        'user' => '/elite-salon/user/dashboard.php'
    ];
    return $dashboards[$role] ?? '/elite-salon/index.php';
}
?>
```

### Login Flow

1. User submits login form (login.php)
2. Credentials validated against database
3. Password verified using `password_verify()`
4. Session variables set:
   - `$_SESSION['user_id']`
   - `$_SESSION['role']`
   - `$_SESSION['name']`
5. Redirect to role-specific dashboard

### Password Security

```php
// Registration: Hash password
$hashed = password_hash($password, PASSWORD_BCRYPT);

// Login: Verify password
if (password_verify($input_password, $stored_hash)) {
    // Login successful
}
```

---

## 📅 Booking System Logic

### Appointment Creation Flow

1. **User selects service and stylist**
2. **System checks stylist availability**
3. **User picks date and time**
4. **Form submitted to server**
5. **Server validates:**
   - Date is not in the past
   - Time slot is available
   - Stylist exists
   - User is logged in
6. **Insert into database**
7. **Send confirmation (email/SMS optional)**

### Code Example (user/appointments.php)

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $stylist_id = $_POST['stylist_id'];
    $service = $_POST['service'];
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];
    
    // Validate availability
    $stmt = $pdo->prepare("SELECT * FROM appointments 
                           WHERE stylist_id = ? 
                           AND appointment_date = ? 
                           AND appointment_time = ?");
    $stmt->execute([$stylist_id, $date, $time]);
    
    if ($stmt->rowCount() > 0) {
        $error = "Time slot already booked";
    } else {
        // Create appointment
        $stmt = $pdo->prepare("INSERT INTO appointments 
                              (user_id, stylist_id, service, appointment_date, appointment_time) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $stylist_id, $service, $date, $time]);
        $success = "Appointment booked successfully!";
    }
}
```

---

## ⚙️ JavaScript Architecture

### Main JavaScript File (assets/js/main.js)

#### **Key Functions:**

1. **Smooth Scrolling**
```javascript
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});
```

2. **Navbar Scroll Effects**
```javascript
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.pageYOffset > 50) {
        navbar.style.boxShadow = '0 4px 30px rgba(0, 0, 0, 0.2)';
    }
});
```

3. **Form Validation**
```javascript
forms.forEach(form => {
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('input[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (field.value === '') {
                isValid = false;
                field.classList.add('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showAlert('danger', 'Please fill in all required fields.');
        }
    });
});
```

4. **Intersection Observer (Scroll Animations)**
```javascript
const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
            observer.unobserve(entry.target);
        }
    });
});

document.querySelectorAll('.service-card, .stats-card').forEach(el => {
    observer.observe(el);
});
```

### JavaScript Best Practices

- ✅ Use vanilla JavaScript (no jQuery dependency)
- ✅ Event delegation for dynamic elements
- ✅ Debounce scroll and resize events
- ✅ Use `const` and `let` (no `var`)
- ✅ Arrow functions for callbacks
- ✅ Async/await for AJAX calls

### DO NOT MODIFY JavaScript IDs

⚠️ **CRITICAL**: The following IDs are used by JavaScript and MUST NOT be changed:

- Form IDs: `loginForm`, `registerForm`, `appointmentForm`
- Button IDs: Any with `onclick` handlers
- Element IDs: Referenced in `main.js`

Changing these will break functionality.

---

## 🚀 Setup & Deployment

### Local Development Setup

#### **Prerequisites:**
- PHP 7.4+ with extensions: mysqli, pdo, session
- MySQL 5.7+ or MariaDB
- Apache or Nginx
- Git (optional)

#### **Step 1: Clone/Extract Project**
```bash
# Option 1: Extract ZIP
unzip elite-salon.zip
cd elite-salon

# Option 2: Git clone (if repository exists)
git clone https://github.com/yourrepo/elite-salon.git
cd elite-salon
```

#### **Step 2: Database Setup**
```bash
# Create database
mysql -u root -p
CREATE DATABASE elite_salon;
USE elite_salon;

# Import schema
SOURCE database.sql;

# Create admin user (optional)
INSERT INTO users (name, email, password, role) 
VALUES ('Admin', 'admin@elite.com', '$2y$10$hashedpassword', 'admin');
```

#### **Step 3: Configure Database Connection**

Edit `includes/db.php`:
```php
$host = 'localhost';
$dbname = 'elite_salon';
$username = 'root';      // Change this
$password = '';          // Change this
```

#### **Step 4: Configure Apache**

**Option A: Using .htaccess (already included)**
```apache
RewriteEngine On
RewriteBase /elite-salon/

# Remove .php extension
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^([^\.]+)$ $1.php [NC,L]
```

**Option B: Virtual Host**
```apache
<VirtualHost *:80>
    ServerName elitesalon.local
    DocumentRoot /var/www/html/elite-salon
    
    <Directory /var/www/html/elite-salon>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### **Step 5: Set Permissions**
```bash
# Linux/Mac
chmod 755 -R elite-salon/
chmod 644 elite-salon/.htaccess

# Make uploads directory writable (if exists)
chmod 777 elite-salon/uploads/
```

#### **Step 6: Test Installation**

Visit: `http://localhost/elite-salon/check.php`

Should display:
- ✅ Database connection OK
- ✅ PHP version OK
- ✅ Session working
- ✅ Files accessible

### Production Deployment

#### **Security Checklist:**

1. ✅ Change database credentials
2. ✅ Use HTTPS (SSL certificate)
3. ✅ Set secure session settings:
   ```php
   ini_set('session.cookie_secure', 1);
   ini_set('session.cookie_httponly', 1);
   ini_set('session.cookie_samesite', 'Strict');
   ```
4. ✅ Disable error display:
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```
5. ✅ Enable CSP headers in .htaccess
6. ✅ Restrict file upload types
7. ✅ Use prepared statements (already implemented)
8. ✅ Validate all user inputs
9. ✅ Set proper file permissions (644 for files, 755 for directories)
10. ✅ Remove development files (check.php, test files)

---

## 🎨 Customization Guide

### Changing Colors

Edit `/assets/css/luxury-theme.css`:

```css
:root {
    --luxury-black: #YOUR_COLOR;
    --luxury-gold: #YOUR_ACCENT;
    /* etc. */
}
```

### Adding New Services

1. **Update database** (if service list is dynamic)
2. **Add service card** in `index.php`:
```php
<div class="col-md-6 col-lg-3 mb-4">
    <div class="service-card">
        <img src="your-image.jpg" alt="Service" class="service-card-img">
        <div class="service-card-body">
            <i class="bi bi-your-icon"></i>
            <h4>Your Service</h4>
            <p>Description</p>
        </div>
    </div>
</div>
```

### Customizing Hero Section

Edit `index.php` hero section:

```html
<section class="hero-section">
    <div class="container">
        <div class="hero-glass-card text-center">
            <h1>Your Custom Title</h1>
            <p class="hero-subtitle">Your Subtitle</p>
            <p class="lead">Your description</p>
            <!-- Buttons -->
        </div>
    </div>
</section>
```

Change background image in `/assets/css/luxury-theme.css`:
```css
.hero-section {
    background: linear-gradient(...),
                url('your-image.jpg') center/cover;
}
```

---

## 🔒 Security Best Practices

### 1. SQL Injection Prevention
✅ **Already Implemented: PDO Prepared Statements**

```php
// GOOD - Prepared statement
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);

// BAD - String concatenation
$query = "SELECT * FROM users WHERE email = '$email'";  // NEVER DO THIS
```

### 2. XSS Prevention

```php
// Escape output
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
```

### 3. CSRF Protection

```php
// Generate token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// In form
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

// Validate
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Invalid request');
}
```

### 4. Password Security

```php
// Strong password requirements
- Minimum 8 characters
- At least one uppercase
- At least one number
- At least one special character

// Hashing
$hashed = password_hash($password, PASSWORD_BCRYPT);
```

---

## ⚡ Performance Optimization

### Frontend Optimization

1. **Image Optimization**
   - Use WebP format with JPG fallback
   - Lazy load images below fold
   - Set appropriate sizes and dimensions

2. **CSS Optimization**
   - Minify CSS files for production
   - Remove unused CSS
   - Use CSS variables for consistency

3. **JavaScript Optimization**
   - Minify JS files
   - Defer non-critical scripts
   - Use event delegation

### Backend Optimization

1. **Database Indexing**
```sql
CREATE INDEX idx_appointments_date ON appointments(appointment_date);
CREATE INDEX idx_users_email ON users(email);
```

2. **Query Optimization**
   - Use LIMIT for pagination
   - Select only needed columns
   - Use JOIN instead of multiple queries

3. **Caching**
   - Enable opcode cache (OPcache)
   - Cache database queries
   - Use session caching

---

## 🐛 Troubleshooting

### Issue: CSS Not Loading / Bootstrap Style Shows

**Solution:**
1. Clear browser cache (Ctrl+F5)
2. Verify CSS load order in `includes/header.php`
3. Check file paths are correct
4. Ensure `luxury-theme.css` loads AFTER Bootstrap

### Issue: Forms Not Submitting

**Solution:**
1. Check JavaScript console for errors
2. Verify form `action` attribute
3. Check PHP error logs
4. Ensure database connection is working

### Issue: Login Redirects to Wrong Dashboard

**Solution:**
1. Check `$_SESSION['role']` value
2. Verify role in database
3. Check `get_dashboard_url()` function in `includes/auth.php`

### Issue: Images Not Displaying

**Solution:**
1. Check image URLs (absolute vs relative paths)
2. Verify images exist on server
3. Check file permissions
4. Clear browser cache

---

## 📚 Additional Resources

- **Bootstrap 5 Docs**: https://getbootstrap.com/docs/5.3/
- **PHP Manual**: https://www.php.net/manual/en/
- **MySQL Docs**: https://dev.mysql.com/doc/
- **MDN Web Docs**: https://developer.mozilla.org/

---

## 🤝 Contributing

When modifying the codebase:

1. **Test thoroughly** before committing
2. **Follow naming conventions** (camelCase for JS, snake_case for PHP)
3. **Comment complex logic**
4. **Update documentation** when changing functionality
5. **Do NOT break existing features**
6. **Maintain the luxury design system**

---

## 📞 Support

For technical support, contact:
- Email: dev@elitesalon.com
- Documentation: This file
- User Guide: USER_GUIDE.md

---

**© 2024 Elite Salon. All rights reserved.**

*This is a technical document for developers. For end-user documentation, see USER_GUIDE.md*
