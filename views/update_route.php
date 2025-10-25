<?php
session_start();
include "../config/db.php";
include "../includes/run_query.php";

// Ensure logged in as deliveryman
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['deliveryman','volunteer'])) {
    header("Location: login.php");
    exit;
}

$queries = loadQueries(__DIR__ . "/../sql/queries.sql");
$deliveryman_id = $_SESSION['user']['user_id'];

// Fetch in-progress assignments for this deliveryman
$assignments = executeQuery($conn, $queries, "select_inprogress_assignments", ["deliveryman_id"=>$deliveryman_id]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignment_id = $_POST['assignment_id'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $location = $_POST['location'];

    // ✅ Validate assignment belongs to deliveryman and is in progress
    $valid = false;
    foreach ($assignments as $a) {
        if ($a['assignment_id'] == $assignment_id) {
            $valid = true;
            break;
        }
    }
    if (!$valid) {
        die("Invalid assignment ID or not in progress.");
    }

    // Update live location
    executeQuery($conn, $queries, "update_courierlocation", [
        "assignment_id" => $assignment_id,
        "latitude" => $latitude,
        "longitude" => $longitude,
        "deliveryman_id" => $deliveryman_id
    ]);

    // Insert route step
    $seq_result = executeQuery($conn, $queries, "get_next_route_sequence", ["assignment_id"=>$assignment_id]);
    $next_seq = 1;
    if (!empty($seq_result) && isset($seq_result[0]['max_seq'])) {
        $next_seq = $seq_result[0]['max_seq'] + 1;
    }

    executeQuery($conn, $queries, "insert_route_step", [
        "assignment_id" => $assignment_id,
        "sequence_no" => $next_seq,
        "location" => $location,
        "latitude" => $latitude,
        "longitude" => $longitude,
        "status" => 'in_transit'
    ]);

    echo "<script>alert('Route Added successfully!'); window.location.href='dashboard.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Delivery Requests</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<?php include '../includes/header.php'; ?>
<body class="pt-24 bg-gray-100"> <!-- pt-24 adds padding-top -->
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow mt-4">
    <h2 class="text-xl font-bold mb-4">Update Route</h2>
    <form method="POST" class="space-y-4">
        <div>
            <label class="block font-medium">Assignment:</label>
            <select name="assignment_id" class="border rounded px-3 py-2 w-full" required>
                <?php foreach ($assignments as $a): ?>
                    <option value="<?= $a['assignment_id'] ?>">
                        <?= htmlspecialchars($a['request_id'] ?? "Reuest id ".$a['assignment_id']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block font-medium">Location Name:</label>
            <input type="text" name="location" class="border rounded px-3 py-2 w-full" placeholder="Enter location" required>
        </div>
        <div>
            <label class="block font-medium">Latitude:</label>
            <input type="text" name="latitude" class="border rounded px-3 py-2 w-full" placeholder="Enter latitude" required>
        </div>
        <div>
            <label class="block font-medium">Longitude:</label>
            <input type="text" name="longitude" class="border rounded px-3 py-2 w-full" placeholder="Enter longitude" required>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Route</button>
    </form>
</div>
</body>

</html>
