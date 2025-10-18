CREATE TABLE IF NOT EXISTS CourierLocation (
    location_id INT PRIMARY KEY AUTO_INCREMENT,
    deliveryman_id INT NOT NULL,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (deliveryman_id) REFERENCES Users(user_id)
);
