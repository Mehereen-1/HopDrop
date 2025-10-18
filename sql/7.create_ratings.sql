CREATE TABLE IF NOT EXISTS Ratings (
    rating_id INT PRIMARY KEY AUTO_INCREMENT,
    request_id INT NOT NULL,
    rated_by INT NOT NULL,
    rated_user INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    feedback TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES DeliveryRequests(request_id),
    FOREIGN KEY (rated_by) REFERENCES Users(user_id),
    FOREIGN KEY (rated_user) REFERENCES Users(user_id)
);
