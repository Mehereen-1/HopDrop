<?php
session_start();
include "../config/db.php";
include "../includes/run_query.php";

// Ensure user is logged in as deliveryman
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'deliveryman' && $_SESSION['user']['role'] !== 'volunteer')) {
    header("Location: login.php");
    exit;
}

$queries = loadQueries(__DIR__ . "/../sql/queries.sql");

// Handle filter
$selected_city_id = $_GET['city_id'] ?? '';

// Fetch cities for dropdown
$cities = executeQuery($conn, $queries, "select_all_cities");

// Fetch available deliveries
$params = ["preferred_type" => ($_SESSION['user']['role'] === 'volunteer') ? 'volunteer' : 'paid'];
$sql_name = "select_available_deliveries";
if ($selected_city_id) {
    $sql_name = "select_available_deliveries_by_city"; // New query with WHERE dr.city_id = :city_id
    $params = [
        "city_id" => $selected_city_id,
        "preferred_type" => ($_SESSION['user']['role'] === 'volunteer') ? 'volunteer' : 'paid'
    ];
}

$available_deliveries = executeQuery($conn, $queries, $sql_name, $params);

// Accept a delivery request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_request'])) {
    $params = [
        "request_id" => $_POST['request_id'],
        "deliveryman_id" => $_SESSION['user']['user_id'],
        "is_volunteer" => ($_POST['type'] == 'volunteer') ? 1 : 0
    ];
    executeQuery($conn, $queries, "accept_delivery_request", $params);

    $statusParams = [
        "request_id" => $_POST['request_id'],
        "status" => 'assigned'   // Set the new status
    ];
    // Update the request status
    executeQuery($conn, $queries, "update_request_status", $statusParams);

    header("Location: available_deliveries.php?city_id=" . $selected_city_id);
    exit;
}
?>

<?php include '../includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Deliveries</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 pt-24">

<div class="max-w-6xl mx-auto p-6">

    <h2 class="text-2xl font-bold text-blue-600 mb-4">Available Deliveries</h2>

    <!-- City Filter -->
    <form method="GET" class="mb-6 flex items-center space-x-4">
        <label class="font-medium">Filter by City:</label>
        <select name="city_id" class="border rounded px-3 py-2">
            <option value="">All Cities</option>
            <?php foreach ($cities as $c): ?>
                <option value="<?= $c['city_id'] ?>" <?= ($c['city_id'] == $selected_city_id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filter</button>
    </form>

    <?php if (empty($available_deliveries)): ?>
        <p class="text-gray-500">No available deliveries at the moment.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse bg-white rounded-2xl shadow-md">
                <thead>
                    <tr class="bg-blue-100 text-blue-800">
                        <th class="px-4 py-2">Request ID</th>
                        <th class="px-4 py-2">Pickup</th>
                        <th class="px-4 py-2">Delivery</th>
                        <th class="px-4 py-2">City</th>
                        <th class="px-4 py-2">Type</th>
                        <th class="px-4 py-2">Created At</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (is_array($available_deliveries) ? $available_deliveries : [] as $d): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2"><?= $d['request_id'] ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($d['pickup_address']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($d['delivery_address']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($d['name'] ?? '-') ?></td>
                            <td class="px-4 py-2"><?= ucfirst($d['preferred_type']) ?></td>
                            <td class="px-4 py-2"><?= $d['created_at'] ?></td>
                            <td class="px-4 py-2">
                                <form method="POST">
                                    <input type="hidden" name="request_id" value="<?= $d['request_id'] ?>">
                                    <input type="hidden" name="type" value="<?= $d['preferred_type'] ?>">
                                    <button type="submit" name="accept_request"
                                            class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                                        Accept
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>
</body>
</html>
