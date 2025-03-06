CREATE DATABASE final;
USE final;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,  
    username VARCHAR(50) NOT NULL UNIQUE,  
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    age INT NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user' NOT NULL,
    imageprofile VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE events (
    eventid INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, 
    event_name VARCHAR(100) NOT NULL,
    date DATETIME NOT NULL,
    location VARCHAR(100) NOT NULL,
    limits INT UNSIGNED NOT NULL,  
    image TEXT DEFAULT NULL, 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,  
    event_id INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    checked_in TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, 
    FOREIGN KEY (event_id) REFERENCES events(eventid) ON DELETE CASCADE
);