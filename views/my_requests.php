<?php
session_start();
include "../config/db.php";
include "../includes/run_query.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

// Load SQL queries
$queries = loadQueries(__DIR__ . "/../sql/queries.sql");
$params = ["user_id" => $_SESSION['user']['user_id']];
$requests = executeQuery($conn, $queries, "select_my_delivery_requests", $params);

// ✅ Ensure $requests is always an array
if (is_string($requests)) {
    // Maybe the query returns JSON or a serialized string
    $decoded = json_decode($requests, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $requests = $decoded;
    } else {
        $requests = [];
    }
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
<title>My Delivery Requests</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen pt-20">
<div class="max-w-5xl mx-auto bg-white shadow-lg rounded-2xl p-8">
    <h1 class="text-3xl font-bold text-blue-600 mb-6 text-center">📦 My Delivery Requests</h1>

    <?php if (empty($requests)): ?>
        <p class="text-gray-500 text-center">No delivery requests found.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-blue-100 text-blue-800">
                        <th class="px-4 py-2 text-left">Request ID</th>
                        <th class="px-4 py-2 text-left">Pickup</th>
                        <th class="px-4 py-2 text-left">Delivery</th>
                        <th class="px-4 py-2 text-left">Type</th>
                        <th class="px-4 py-2 text-left">City</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Created At</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $r): ?>
                        <?php
                        // ✅ Safety check: convert to array if JSON or string
                        if (is_string($r)) {
                            $r = json_decode($r, true);
                        }
                        if (!is_array($r)) continue;
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium"><?= htmlspecialchars($r['request_id'] ?? '-') ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($r['pickup_address'] ?? '-') ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($r['delivery_address'] ?? '-') ?></td>
                            <td class="px-4 py-2"><?= ucfirst($r['preferred_type'] ?? '-') ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($r['city_name'] ?? '-') ?></td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-white text-sm
                                    <?= ($r['status'] ?? '') === 'completed' ? 'bg-green-500' :
                                       (($r['status'] ?? '') === 'pending' ? 'bg-yellow-500' :
                                       (($r['status'] ?? '') === 'cancelled' ? 'bg-red-500' : 'bg-blue-500')) ?>">
                                    <?= ucfirst($r['status'] ?? 'Unknown') ?>
                                </span>
                            </td>
                            <td class="px-4 py-2 text-gray-600 text-sm"><?= htmlspecialchars($r['created_at'] ?? '-') ?></td>
                            <td class="px-4 py-2">
                                <a href="edit_request.php?id=<?= $r['request_id'] ?>" class="text-blue-600 hover:underline">Edit</a>
                                <a href="delete_request.php?id=<?= $r['request_id'] ?>" class="text-red-600 hover:underline ml-2" onclick="return confirm('Are you sure you want to delete this request?');">Delete</a>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="mt-6 text-center">
        <a href="dashboard.php" class="text-blue-600 hover:underline">⬅ Back to Dashboard</a>
    </div>
</div>
</body>
</html>
