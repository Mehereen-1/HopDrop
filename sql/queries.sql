-- name: insert_user
INSERT INTO Users (name, phone, email, password_hash, role, address, city, availability_status)
VALUES (:name, :phone, :email, SHA2(:password, 256), :role, :address, :city, :availability_status);

-- name: get_all_users
SELECT user_id, name, phone, email, role, city, availability_status
FROM Users
ORDER BY created_at DESC;

-- name: get_users_by_city
SELECT user_id, name, role
FROM Users
WHERE city = :city
ORDER BY name ASC;

-- name: available_deliverymen
SELECT user_id, name, city
FROM Users
WHERE role = 'deliveryman' AND availability_status = 'available';

-- name: insert_delivery_request
INSERT INTO DeliveryRequests (sender_id, receiver_id, pickup_address, delivery_address, package_description, preferred_type)
VALUES (:sender_id, :receiver_id, :pickup_address, :delivery_address, :package_description, :preferred_type);

-- name: get_pending_requests
SELECT request_id, sender_id, pickup_address, delivery_address, status
FROM DeliveryRequests
WHERE status = 'pending'
ORDER BY created_at DESC;

-- name: assign_request
INSERT INTO Assignments (request_id, deliveryman_id, is_volunteer)
VALUES (:request_id, :deliveryman_id, :is_volunteer);

-- name: get_assignments_with_user_details
SELECT 
  a.assignment_id,
  u.name AS deliveryman_name,
  dr.package_description,
  dr.status AS request_status
FROM Assignments a
INNER JOIN Users u ON a.deliveryman_id = u.user_id
INNER JOIN DeliveryRequests dr ON a.request_id = dr.request_id
ORDER BY a.accepted_at DESC;

-- name: update_request_status
UPDATE DeliveryRequests
SET status = :status
WHERE request_id = :request_id;

-- name: mark_assignment_completed
UPDATE Assignments
SET completed_at = CURRENT_TIMESTAMP
WHERE assignment_id = :assignment_id;

-- name: insert_payment
INSERT INTO Payments (assignment_id, amount, method, status)
VALUES (:assignment_id, :amount, :method, 'pending');

-- name: complete_payment
UPDATE Payments
SET status = 'paid', paid_at = CURRENT_TIMESTAMP
WHERE payment_id = :payment_id;

-- name: get_total_payments_by_method
SELECT method, COUNT(*) AS total_transactions, SUM(amount) AS total_amount
FROM Payments
WHERE status = 'paid'
GROUP BY method
ORDER BY total_amount DESC;

-- name: get_unpaid_payments
SELECT p.payment_id, u.name AS deliveryman_name, p.amount
FROM Payments p
LEFT JOIN Assignments a ON p.assignment_id = a.assignment_id
LEFT JOIN Users u ON a.deliveryman_id = u.user_id
WHERE p.status = 'pending'
ORDER BY p.amount DESC;

-- name: insert_route
INSERT INTO Routes (assignment_id, sequence_no, location, latitude, longitude, status)
VALUES (:assignment_id, :sequence_no, :location, :latitude, :longitude, 'pending');

-- name: get_route_for_assignment
SELECT location, latitude, longitude, status
FROM Routes
WHERE assignment_id = :assignment_id
ORDER BY sequence_no ASC;

-- name: update_route_status
UPDATE Routes
SET status = :status
WHERE route_id = :route_id;

-- name: insert_location_update
INSERT INTO CourierLocation (deliveryman_id, latitude, longitude)
VALUES (:deliveryman_id, :latitude, :longitude);

-- name: get_latest_courier_location
SELECT deliveryman_id, latitude, longitude, updated_at
FROM CourierLocation
WHERE deliveryman_id = :deliveryman_id
ORDER BY updated_at DESC
LIMIT 1;

-- name: insert_rating
INSERT INTO Ratings (request_id, rated_by, rated_user, rating, feedback)
VALUES (:request_id, :rated_by, :rated_user, :rating, :feedback);

-- name: get_average_rating_for_user
SELECT rated_user, AVG(rating) AS avg_rating, COUNT(*) AS total_reviews
FROM Ratings
WHERE rated_user = :rated_user
GROUP BY rated_user;

-- name: get_feedback_with_users
SELECT 
  r.rating_id,
  r.rating,
  r.feedback,
  rb.name AS rated_by_name,
  ru.name AS rated_user_name
FROM Ratings r
LEFT JOIN Users rb ON r.rated_by = rb.user_id
LEFT JOIN Users ru ON r.rated_user = ru.user_id
ORDER BY r.created_at DESC;

-- name: delete_cancelled_requests
DELETE FROM DeliveryRequests
WHERE status = 'cancelled' AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY);

-- name: get_most_active_deliverymen
SELECT 
  u.user_id,
  u.name,
  COUNT(a.assignment_id) AS total_deliveries
FROM Users u
INNER JOIN Assignments a ON u.user_id = a.deliveryman_id
GROUP BY u.user_id
ORDER BY total_deliveries DESC
LIMIT 5;

-- name: get_citywise_request_count
SELECT city, COUNT(dr.request_id) AS total_requests
FROM DeliveryRequests dr
INNER JOIN Users u ON dr.sender_id = u.user_id
GROUP BY city
ORDER BY total_requests DESC;

-- name: right_join_example
SELECT 
  u.name AS deliveryman_name,
  a.assignment_id
FROM Users u
RIGHT JOIN Assignments a ON u.user_id = a.deliveryman_id;

-- name: full_join_simulation
SELECT 
  u.name AS deliveryman_name,
  a.assignment_id
FROM Users u
LEFT JOIN Assignments a ON u.user_id = a.deliveryman_id
UNION
SELECT 
  u.name AS deliveryman_name,
  a.assignment_id
FROM Users u
RIGHT JOIN Assignments a ON u.user_id = a.deliveryman_id;

-- name: get_recent_requests_with_sender
SELECT 
  dr.request_id,
  s.name AS sender_name,
  dr.pickup_address,
  dr.delivery_address,
  dr.status
FROM DeliveryRequests dr
JOIN Users s ON dr.sender_id = s.user_id
ORDER BY dr.created_at DESC
LIMIT 10;

-- name: get_completed_deliveries_in_last_30_days
SELECT 
  a.assignment_id,
  u.name AS deliveryman_name,
  dr.package_description
FROM Assignments a
JOIN Users u ON a.deliveryman_id = u.user_id
JOIN DeliveryRequests dr ON a.request_id = dr.request_id
WHERE a.completed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY);
