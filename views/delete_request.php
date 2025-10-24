<?php
session_start();
include "../config/db.php";
include "../includes/run_query.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("❌ Request ID missing");
}

$queries = loadQueries(__DIR__ . "/../sql/queries.sql");
$params = ["request_id" => $_GET['id']];

// Optional: Verify that the request belongs to the current user
$check = executeQuery($conn, $queries, "select_delivery_request_by_id", ["request_id" => $_GET['id']]);
if (empty($check) || $check[0]['sender_id'] != $_SESSION['user']['user_id']) {
    die("❌ Unauthorized");
}

$result = executeQuery($conn, $queries, "delete_delivery_request", $params);

if ($result === TRUE) {
    header("Location: my_requests.php?msg=deleted");
} else {
    echo "❌ Failed to delete: " . $result['error'] ?? '';
}
?>
