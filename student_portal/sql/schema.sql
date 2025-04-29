-- Database schema for student portal

CREATE DATABASE IF NOT EXISTS student_portal;
USE student_portal;

-- Table to store valid IDs for registration verification
CREATE TABLE valid_ids (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_number VARCHAR(50) NOT NULL UNIQUE,
    role ENUM('student', 'teacher') NOT NULL
);

-- Table to store registered users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    id_number VARCHAR(50) NOT NULL,
    role ENUM('student', 'teacher') NOT NULL,
    email VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_number) REFERENCES valid_ids(id_number)
);

-- Table to store grades raw data
CREATE TABLE grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL,
    grade_data TEXT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES users(id_number)
);
