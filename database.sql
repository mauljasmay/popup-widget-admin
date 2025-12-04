-- Popup Widget Admin Database Schema

CREATE DATABASE IF NOT EXISTS popup_widget_admin;
USE popup_widget_admin;

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin', 'editor') DEFAULT 'admin',
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Popup widgets table
CREATE TABLE IF NOT EXISTS popup_widgets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('modal', 'slide_in', 'notification', 'exit_intent') DEFAULT 'modal',
    position ENUM('center', 'top_left', 'top_right', 'bottom_left', 'bottom_right') DEFAULT 'center',
    width INT DEFAULT 400,
    height INT DEFAULT 300,
    background_color VARCHAR(7) DEFAULT '#ffffff',
    text_color VARCHAR(7) DEFAULT '#333333',
    button_text VARCHAR(50) DEFAULT 'Close',
    button_color VARCHAR(7) DEFAULT '#007bff',
    show_close_button BOOLEAN DEFAULT TRUE,
    auto_close INT DEFAULT 0, -- seconds, 0 = disabled
    show_after INT DEFAULT 0, -- seconds after page load
    show_on_scroll BOOLEAN DEFAULT FALSE,
    scroll_percentage INT DEFAULT 50,
    show_on_exit BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    target_pages TEXT, -- JSON array of page URLs
    exclude_pages TEXT, -- JSON array of page URLs to exclude
    start_date DATE,
    end_date DATE,
    max_shows_per_user INT DEFAULT 0, -- 0 = unlimited
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Popup analytics table
CREATE TABLE IF NOT EXISTS popup_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    widget_id INT NOT NULL,
    page_url VARCHAR(500) NOT NULL,
    user_ip VARCHAR(45),
    user_agent TEXT,
    action ENUM('show', 'close', 'click') NOT NULL,
    session_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (widget_id) REFERENCES popup_widgets(id) ON DELETE CASCADE
);

-- Popup images table
CREATE TABLE IF NOT EXISTS popup_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    widget_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (widget_id) REFERENCES popup_widgets(id) ON DELETE CASCADE
);

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, email, password_hash, full_name, role) 
VALUES ('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Admin', 'super_admin')
ON DUPLICATE KEY UPDATE username = username;