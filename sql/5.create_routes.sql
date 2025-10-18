CREATE TABLE IF NOT EXISTS Routes (
    route_id INT PRIMARY KEY AUTO_INCREMENT,
    assignment_id INT NOT NULL,
    sequence_no INT NOT NULL, -- 1 = pickup, n = waypoints, last = drop
    location VARCHAR(255) NOT NULL,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    status ENUM('pending', 'in_transit', 'completed') DEFAULT 'pending',
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assignment_id) REFERENCES Assignments(assignment_id)
);
