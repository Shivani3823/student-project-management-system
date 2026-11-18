-- Student Project Management System
-- Import this in phpMyAdmin (http://localhost/phpmyadmin) -> Import
CREATE DATABASE IF NOT EXISTS spms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE spms;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','student') NOT NULL DEFAULT 'student',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  title VARCHAR(150) NOT NULL,
  description TEXT,
  tech_stack VARCHAR(150),
  guide VARCHAR(100),
  status ENUM('Pending','In Progress','Completed') NOT NULL DEFAULT 'Pending',
  progress TINYINT UNSIGNED NOT NULL DEFAULT 0,
  start_date DATE NULL,
  end_date DATE NULL,
  feedback TEXT NULL,
  grade VARCHAR(5) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);
-- The default admin (admin@spms.com / admin123) is created automatically
-- the first time you open the website.
