<?php
session_start();
include "../config/db.php";
include "../includes/run_query.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$queries = loadQueries(__DIR__ . "/../sql/queries.sql");

$assignment_id = $_GET['assignment_id'] ?? null;
if (!$assignment_id) {
    die("Assignment ID is required");
}

// Fetch all route steps for this assignment
$routes = executeQuery($conn, $queries, "select_assignment_routes", ["assignment_id"=>$assignment_id]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Track Package</title>
<style>
body { font-family: Arial, sans-serif; background:#f0f2f5; }
.container { max-width:600px; margin:50px auto; background:white; padding:20px; border-radius:12px; }
.step { padding:15px; border-left:4px solid #3498db; margin-bottom:10px; background:#fafafa; border-radius:6px; position:relative; }
.step::before { content:""; position:absolute; left:-6px; top:15px; width:12px; height:12px; background:#3498db; border-radius:50%; }
.status-picked_up { border-color:#f1c40f; }
.status-in_transit { border-color:#3498db; }
.status-delivered { border-color:#2ecc71; }
</style>
</head>
<body>
<div class="container">
<h2>Package Progress</h2>

<?php if(empty($routes)): ?>
<p>No progress yet.</p>
<?php else: ?>
<?php foreach($routes as $r): ?>
<div class="step status-<?= $r['status'] ?>">
    <strong>Step <?= $r['sequence_no'] ?>:</strong> <?= htmlspecialchars($r['location']) ?><br>
    Lat: <?= $r['latitude'] ?>, Lng: <?= $r['longitude'] ?><br>
    Status: <?= ucfirst($r['status']) ?><br>
    <small><?= $r['timestamp'] ?></small>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>
</body>
</html>
