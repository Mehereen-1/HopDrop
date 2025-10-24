-- Create Cities table
CREATE TABLE IF NOT EXISTS Cities (
    city_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- Insert major cities of Bangladesh
INSERT INTO Cities (name) VALUES
('Dhaka'),
('Chattogram'),
('Khulna'),
('Rajshahi'),
('Barishal'),
('Sylhet'),
('Rangpur'),
('Mymensingh');
