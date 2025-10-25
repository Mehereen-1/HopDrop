<?php
session_start();
include "../config/db.php";
include "../includes/run_query.php";

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['user_id'];
$queries = loadQueries(__DIR__ . "/../sql/queries.sql");

// Fetch all delivery requests for this customer
$requests = executeQuery($conn, $queries, "select_my_delivery_requests", ["user_id" => $user_id]);

// Ensure $requests is always an array
if (is_string($requests)) {
    $decoded = json_decode($requests, true);
    $requests = (json_last_error() === JSON_ERROR_NONE) ? $decoded : [];
} elseif (!is_array($requests)) {
    $requests = [];
}

?>

<?php include '../includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Parcels</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<style>
body { font-family: Arial, sans-serif; background:#f0f2f5; padding-top:80px; }
.container { max-width: 900px; margin: 0 auto; padding: 20px; }
.card { background:white; padding:15px; border-radius:12px; margin-bottom:15px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
.card h3 { margin:0 0 5px 0; }
.card p { margin: 2px 0; }
.track-btn { display:inline-block; margin-top:5px; padding:5px 10px; background:#3498db; color:white; border-radius:6px; text-decoration:none; }
.track-btn:hover { background:#2980b9; }
.status-pending { color: #e67e22; font-weight: bold; }
.status-in_progress { color: #3498db; font-weight: bold; }
.status-completed { color: #2ecc71; font-weight: bold; }
</style>
</head>
<body>

<div class="container">
    <h2>My Parcels</h2>

    <?php if(empty($requests)): ?>
        <p>No delivery requests found.</p>
    <?php else: ?>
        <?php foreach($requests as $r): ?>
            <div class="card">
                <h3>Request ID: <?= htmlspecialchars($r['request_id']) ?></h3>
                <p>Pickup: <?= htmlspecialchars($r['pickup_address']) ?></p>
                <p>Delivery: <?= htmlspecialchars($r['delivery_address']) ?></p>
                <p>Type: <?= htmlspecialchars($r['preferred_type']) ?></p>
                <p>Status: <span class="status-<?= strtolower($r['status']) ?>"><?= htmlspecialchars(ucfirst($r['status'])) ?></span></p>
                <p>Created at: <?= htmlspecialchars($r['created_at']) ?></p>
                <!-- <a class="track-btn" href="track_package.php?assignment_id=<?= $r['assignment_id'] ?? $r['request_id'] ?>">Track Package</a> -->
                 <div class="mt-2 space-x-2">
            <a class="track-btn" href="track_package.php?assignment_id=<?= $r['assignment_id'] ?? $r['request_id'] ?>">Track Package</a>
            <a class="track-btn bg-green-500 hover:bg-green-600" 
               href="current_location.php?assignment_id=<?= $r['assignment_id'] ?? $r['request_id'] ?>">View Current Location</a>
        </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
