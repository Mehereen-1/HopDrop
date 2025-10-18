CREATE TABLE IF NOT EXISTS Users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'deliveryman', 'volunteer') NOT NULL,
    address TEXT,
    city VARCHAR(50) NOT NULL, -- Intracity restriction
    availability_status ENUM('available', 'unavailable', 'busy') DEFAULT 'unavailable',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
