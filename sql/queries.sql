-- name: select_all_users
SELECT * FROM Users;

-- name: customers_in_city
SELECT * FROM Users WHERE city = 'Dhaka' ORDER BY name ASC;