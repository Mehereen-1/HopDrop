<?php
session_start();
include "../config/db.php";
include "../includes/run_query.php";
// Ensure user is logged in
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}
$queries = loadQueries(__DIR__ . "/../sql/queries.sql");
$message = "";
$params = [];
$result = executeQuery($conn, $queries, "add_city_to_delivery_requests", $params);

if ($result === TRUE) {
    $message = "✅ Altered table successfully!";
} elseif (is_array($result) && isset($result['error'])) {
    $message = "❌ " . $result['error'];
}
echo $message;
?>