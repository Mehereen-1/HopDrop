CREATE TABLE IF NOT EXISTS Assignments (
    assignment_id INT PRIMARY KEY AUTO_INCREMENT,
    request_id INT NOT NULL,
    deliveryman_id INT NOT NULL,
    is_volunteer BOOLEAN NOT NULL,
    accepted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (request_id) REFERENCES DeliveryRequests(request_id),
    FOREIGN KEY (deliveryman_id) REFERENCES Users(user_id)
);
