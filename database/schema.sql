-- Create database if not exists
CREATE DATABASE IF NOT EXISTS pofinfraa_db;
USE pofinfraa_db;

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Company information table
CREATE TABLE IF NOT EXISTS company_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    about_text TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Projects table
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    completion_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Services table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Slider images table
CREATE TABLE IF NOT EXISTS slider_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_status BOOLEAN DEFAULT false
);

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert default company information
INSERT INTO company_info (company_name, email, phone, address, about_text) VALUES 
(
    'POFINFRAA',
    'info@pofinfraa.com',
    '+91 84597-00000',
    'B-290, Street Number-1, Chattarpur Enclave Phase 2, New Delhi - 110074',
    'POFINFRAA has been at the forefront of infrastructure development, delivering excellence in construction and project management. Our commitment to quality, innovation, and sustainability has made us a trusted partner in building tomorrow\'s infrastructure.'
);

-- Insert sample services
INSERT INTO services (title, description, icon) VALUES
('Construction Management', 'Professional construction management services ensuring project success from inception to completion.', 'fa-building'),
('Infrastructure Development', 'Comprehensive infrastructure development solutions for modern urban needs.', 'fa-road'),
('Project Planning', 'Strategic project planning and execution with attention to detail.', 'fa-chart-line');

-- Insert sample projects
INSERT INTO projects (title, description, image_url, category, completion_date) VALUES
('Modern Office Complex', 'State-of-the-art office complex with sustainable features.', 'project1.jpg', 'Commercial', '2023-12-01'),
('Highway Extension', 'Major highway extension project improving connectivity.', 'project2.jpg', 'Infrastructure', '2023-10-15'),
('Residential Township', 'Premium residential township with modern amenities.', 'project3.jpg', 'Residential', '2024-01-20');

-- Insert sample slider images
INSERT INTO slider_images (title, image_url, active) VALUES
('Modern Infrastructure', 'slider1.jpg', true),
('Quality Construction', 'slider2.jpg', true),
('Innovative Design', 'slider3.jpg', true);
