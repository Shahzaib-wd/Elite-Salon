-- Elite Salon Database Schema
-- Create database and tables

CREATE DATABASE IF NOT EXISTS elite_salon;
USE elite_salon;

-- Users table (Admin, Stylist, Receptionist, User/Customer)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'stylist', 'receptionist', 'user') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Services table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    duration INT NOT NULL COMMENT 'Duration in minutes',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Staff table (additional info for stylists)
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    specialization VARCHAR(255),
    experience_years INT,
    rating DECIMAL(3, 2) DEFAULT 0.00,
    bio TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Appointments table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    stylist_id INT,
    service_id INT,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (stylist_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_stylist_id (stylist_id),
    INDEX idx_appointment_date (appointment_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT,
    user_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'card', 'online', 'other') DEFAULT 'cash',
    status ENUM('pending', 'completed', 'refunded', 'failed') DEFAULT 'pending',
    transaction_id VARCHAR(100),
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_appointment_id (appointment_id),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inventory table
CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    quantity INT NOT NULL DEFAULT 0,
    unit_price DECIMAL(10, 2) NOT NULL,
    supplier VARCHAR(100),
    reorder_level INT DEFAULT 10,
    status ENUM('in_stock', 'low_stock', 'out_of_stock') DEFAULT 'in_stock',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, password_hash, role, status) VALUES 
('Admin User', 'admin@elitesalon.com', '$2y$10$qVr.N1UCS84yG0piS5fMseXbkGK6HIrNYTqJd8VfaT5nfP7/T0gqK', 'admin', 'active');

-- Insert sample stylist (password: stylist123)
INSERT INTO users (name, email, password_hash, phone, role, status) VALUES 
('Sarah Johnson', 'stylist@elitesalon.com', '$2y$10$LGu3CJpPReFq33Rr0tzyTek55J1h5r9R1b3idfsAWWUO54PAVmz.G', '555-0101', 'stylist', 'active');

-- Insert sample receptionist (password: receptionist123)
INSERT INTO users (name, email, password_hash, phone, role, status) VALUES 
('Emily Davis', 'receptionist@elitesalon.com', '$2y$10$XLIAARVGFIlAYVVuicd/geY/tx.e97Y9UM/Wq.Ob7YARUnM4yK9vi', '555-0102', 'receptionist', 'active');

-- Insert sample customer (password: user123)
INSERT INTO users (name, email, password_hash, phone, role, status) VALUES 
('John Smith', 'user@elitesalon.com', '$2y$10$ies8aCY6JzMizjCD1LIi6eR2uQB5bB8uy/eztewP5XCF7hoYVCNJa', '555-0103', 'user', 'active');

-- Insert staff details for stylist
INSERT INTO staff (user_id, specialization, experience_years, rating, bio) VALUES 
(2, 'Hair Styling & Coloring', 5, 4.8, 'Experienced hair stylist specializing in modern cuts and color techniques.');

-- Insert sample services
INSERT INTO services (name, description, price, duration) VALUES 
('Haircut - Men', 'Professional men\'s haircut with styling', 25.00, 30),
('Haircut - Women', 'Women\'s haircut with wash and blow dry', 45.00, 60),
('Hair Coloring', 'Full hair coloring service', 80.00, 120),
('Hair Treatment', 'Deep conditioning hair treatment', 35.00, 45),
('Styling', 'Special occasion hair styling', 50.00, 60),
('Beard Trim', 'Professional beard trimming and shaping', 15.00, 20),
('Manicure', 'Complete nail care and polish', 30.00, 45),
('Pedicure', 'Foot care and nail polish', 40.00, 60);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES 
('salon_name', 'Elite Salon', 'Salon business name'),
('salon_email', 'info@elitesalon.com', 'Salon contact email'),
('salon_phone', '555-0100', 'Salon contact phone'),
('salon_address', '123 Beauty Street, Style City, ST 12345', 'Salon address'),
('working_hours_start', '09:00', 'Salon opening time'),
('working_hours_end', '20:00', 'Salon closing time'),
('booking_advance_days', '30', 'How many days in advance customers can book'),
('cancellation_hours', '24', 'Minimum hours before appointment to cancel');
