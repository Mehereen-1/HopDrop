-- name: insert_user
INSERT INTO Users (name, phone, email, password_hash, role, address, city, availability_status)
VALUES (:name, :phone, :email, SHA2(:password, 256), :role, :address, :city, :availability_status);

-- name: login_user
SELECT * FROM Users
WHERE email = :email AND password_hash = :password_hash
LIMIT 1;

-- name: insert_delivery_request
INSERT INTO DeliveryRequests 
(sender_id, receiver_id, pickup_address, delivery_address, city_id, package_description, preferred_type)
VALUES (:sender_id, :receiver_id, :pickup_address, :delivery_address, :city_id, :package_description, :preferred_type);

-- name: add_city_to_delivery_requests
ALTER TABLE DeliveryRequests
ADD COLUMN city VARCHAR(50) NOT NULL AFTER delivery_address;

-- name: add_city_fk_to_deliveryrequests
ALTER TABLE DeliveryRequests
ADD COLUMN city_id INT,
ADD CONSTRAINT fk_delivery_city
    FOREIGN KEY (city_id)
    REFERENCES Cities(city_id)
    ON DELETE SET NULL;

-- name: alter_deliveryrequests_receiver
ALTER TABLE DeliveryRequests DROP FOREIGN KEY deliveryrequests_ibfk_2;


-- name: new_fk_delivery_receiver
ALTER TABLE DeliveryRequests 
MODIFY receiver_id INT NULL,
ADD CONSTRAINT fk_delivery_receiver
FOREIGN KEY (receiver_id) REFERENCES Users(user_id)
ON DELETE SET NULL;

-- name: select_my_delivery_requests
SELECT 
    dr.*,
    (
        SELECT c.name 
        FROM Cities c 
        WHERE c.city_id = dr.city_id
        LIMIT 1
    ) AS city_name
FROM DeliveryRequests dr
WHERE dr.sender_id = :user_id
ORDER BY dr.created_at DESC;

-- name: select_delivery_request_by_id
SELECT * FROM DeliveryRequests WHERE request_id = :request_id;

-- name: delete_delivery_request
DELETE FROM DeliveryRequests WHERE request_id = :request_id;

-- name: update_delivery_request
UPDATE DeliveryRequests
SET pickup_address = :pickup_address,
    delivery_address = :delivery_address,
    city_id = :city_id,
    package_description = :package_description,
    preferred_type = :preferred_type
WHERE request_id = :request_id;

-- name: select_all_cities
SELECT city_id, name FROM Cities ORDER BY name;

-- name: select_available_deliveries
SELECT 
    dr.request_id,
    dr.pickup_address,
    dr.delivery_address,
    dr.package_description,
    dr.preferred_type,
    c.name,
    dr.status,
    dr.created_at
FROM DeliveryRequests dr
LEFT JOIN Cities c ON dr.city_id = c.city_id
WHERE dr.status = 'pending' AND (dr.preferred_type = 'paid' OR dr.preferred_type = 'volunteer')
ORDER BY dr.created_at ASC;

-- name: select_available_deliveries_by_city
SELECT dr.*, c.name 
FROM deliveryrequests dr
JOIN cities c ON dr.city_id = c.city_id
WHERE dr.status = 'pending'
  AND dr.city_id = :city_id
  AND dr.request_id NOT IN (
      SELECT a.request_id 
      FROM assignments a
  )
  ORDER BY dr.created_at ASC;

-- name: accept_delivery_request
INSERT INTO Assignments (request_id, deliveryman_id, is_volunteer)
VALUES (:request_id, :deliveryman_id, :is_volunteer);

-- name: update_request_status
UPDATE DeliveryRequests
SET status = :status
WHERE request_id = :request_id;

-- name: select_my_assignments
SELECT a.*, dr.pickup_address, dr.delivery_address, dr.status, dr.preferred_type, dr.created_at, dr.request_id
FROM Assignments a
JOIN DeliveryRequests dr ON a.request_id = dr.request_id
WHERE a.deliveryman_id = :deliveryman_id
ORDER BY a.accepted_at DESC;

-- name: select_routes_for_assignment
SELECT *
FROM Routes
WHERE assignment_id = :assignment_id
ORDER BY sequence_no ASC;


-- name: select_routes_for_assignment
SELECT location, latitude, longitude, status, sequence_no
FROM Routes
WHERE assignment_id = :assignment_id
ORDER BY sequence_no ASC;

-- name: select_all_assignments_for_routes
SELECT da.assignment_id, dr.pickup_address
FROM assignments da
JOIN deliveryrequests dr ON da.request_id = dr.request_id
ORDER BY da.assignment_id DESC;

-- name: insert_route_point
INSERT INTO routes (assignment_id, location, latitude, longitude, status, sequence_no)
VALUES (:assignment_id, :location, :latitude, :longitude, :status, :sequence_no);


-- name: insert_route
INSERT INTO routes (assignment_id, latitude, longitude, route_details)
VALUES (:assignment_id, :latitude, :longitude, :route_details);

-- name: select_assignments_by_deliveryman
SELECT da.assignment_id, dr.pickup_address
FROM assignments da
JOIN deliveryrequests dr ON da.request_id = dr.request_id
WHERE da.deliveryman_id = :deliveryman_id;

-- name: select_assignments_by_deliveryman_detailed
SELECT 
    da.assignment_id, 
    da.request_id,
    da.status, 
    dr.pickup_address, 
    dr.delivery_address, 
    c.name,
    da.deliveryman_id
FROM assignments da
JOIN deliveryrequests dr ON da.request_id = dr.request_id
JOIN cities c ON dr.city_id = c.city_id
GROUP BY da.assignment_id, da.status, dr.pickup_address, dr.delivery_address, c.name, da.deliveryman_id
HAVING da.deliveryman_id = :deliveryman_id
ORDER BY da.assignment_id DESC;



-- name: mark_assignment_completed
UPDATE Assignments
SET completed_at = NOW()
WHERE assignment_id = :assignment_id;

-- name: update_assignment_status
UPDATE assignments
SET status = :status
WHERE assignment_id = :assignment_id;

-- name: update_delivery_request_status
UPDATE deliveryrequests
SET status = :status
WHERE request_id = :request_id;




-- name: update_user_role
UPDATE Users
SET role = :role
WHERE user_id = :user_id;

-- name: select_all_ratings
SELECT r.rating_id, r.rating, r.feedback, r.created_at,
       ru.name AS rated_user_name, ru.city AS rated_user_city
FROM Ratings r
JOIN Users ru ON r.rated_user = ru.user_id
ORDER BY r.created_at DESC;

-- name: select_trending_users
SELECT ru.user_id, ru.name, ru.city, AVG(r.rating) AS avg_rating, COUNT(r.rating_id) AS rating_count
FROM Ratings r
JOIN Users ru ON r.rated_user = ru.user_id
GROUP BY ru.user_id, ru.name, ru.city
ORDER BY rating_count DESC, avg_rating DESC
LIMIT 5;


-- name: insert_rating
INSERT INTO Ratings (request_id, rated_by, rated_user, rating, feedback, created_at)
VALUES (:request_id, :rated_by, :rated_user, :rating, :feedback, NOW());


-- name: select_request_by_id
SELECT dr.request_id, dr.deliveryman_id, u.name AS deliveryman_name
FROM DeliveryRequests dr
JOIN Users u ON dr.deliveryman_id = u.user_id
WHERE dr.request_id = :request_id;
