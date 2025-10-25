<?php
session_start();
include "../config/db.php";
include "../includes/run_query.php";

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Load queries
$queries = loadQueries(__DIR__ . "/../sql/queries.sql");

// Get current temporary role from DB
$currentRole = $_SESSION['user']['role']; // original role in DB
$tempRole = $_SESSION['user']['temp_role'] ?? 'none';

// Toggle logic: cycle through 'none' → 'deliveryman' → 'volunteer' → 'none'
if ($currentRole === 'volunteer') {
    $newRole = 'deliveryman';
} elseif ($currentRole === 'deliveryman') {
    $newRole = 'volunteer';
} else {
    $newRole = 'customer';
}

// Update the database: store temporary role
$params = [
    "user_id" => $_SESSION['user']['user_id'],
    "role" => $newRole
];
executeQuery($conn, $queries, "update_user_role", $params);

// Update session
$_SESSION['user']['role'] = $newRole;

// Redirect back to dashboard with message
header("Location: dashboard.php?role=$newRole");
exit;
