<?php
session_start();
include "../config/db.php";
include "../includes/run_query.php";

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Get assignment ID from URL
$assignment_id = $_GET['assignment_id'] ?? null;
if (!$assignment_id) {
    die("Assignment ID is required.");
}

// Load queries
$queries = loadQueries(__DIR__ . "/../sql/queries.sql");

// Fetch the latest location
$locationData = executeQuery($conn, $queries, "select_live_locations", ["assignment_id" => $assignment_id]);

if (empty($locationData)) {
    die("No location data found for this assignment.");
}

// Get latitude and longitude
$lat = $locationData[0]['latitude'];
$lng = $locationData[0]['longitude'];

// Encode values for URL
$lat = urlencode($lat);
$lng = urlencode($lng);

// Redirect to Google Maps with proper pin
$mapUrl = "https://www.google.com/maps/search/?api=1&query={$lat},{$lng}";
header("Location: $mapUrl");
exit;
?>
