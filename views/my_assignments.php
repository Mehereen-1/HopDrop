<?php
session_start();
include "../config/db.php";
include "../includes/run_query.php";

// Ensure user is deliveryman
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'customer') {
    header("Location: login.php");
    exit;
}

$queries = loadQueries(__DIR__ . "/../sql/queries.sql");
$user_id = $_SESSION['user']['user_id'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $params = [
        "assignment_id" => $_POST['assignment_id'],
        "status" => $_POST['status']
    ];
    executeQuery($conn, $queries, "update_assignment_status", $params);

    $deliveryParams = [
        "request_id" => $_POST['request_id'],
        "status" => $_POST['status']
    ];

    $result = executeQuery($conn, $queries, "update_delivery_request_status", $deliveryParams);
    if ($result !== TRUE) {
        die("DeliveryRequest update failed: " . $result['error'] ?? 'Unknown error');
    }

    header("Location: my_assignments.php");
    exit;
}

// Fetch assignments for deliveryman
$params = ["deliveryman_id" => $user_id];
$assignments = executeQuery($conn, $queries, "select_assignments_by_deliveryman_detailed", $params);
?>

<?php include "../includes/header.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Assignments</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 pt-24">

<div class="max-w-6xl mx-auto p-6">
    <h2 class="text-2xl font-bold text-blue-600 mb-4">My Assignments</h2>

    <?php if (empty($assignments)): ?>
        <p class="text-gray-500">You currently have no assignments.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse bg-white rounded-2xl shadow-md">
                <thead>
                    <tr class="bg-blue-100 text-blue-800">
                        <th class="px-4 py-2">Assignment ID</th>
                        <th class="px-4 py-2">Request ID</th>
                        <th class="px-4 py-2">Pickup</th>
                        <th class="px-4 py-2">Delivery</th>
                        <th class="px-4 py-2">City</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignments as $a): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2"><?= $a['assignment_id'] ?></td>
                            <td class="px-4 py-2"><?= $a['request_id'] ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($a['pickup_address']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($a['delivery_address']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($a['name'] ?? '-') ?></td>
                            <td class="px-4 py-2 font-medium <?= 
                                $a['status'] == 'completed' ? 'text-green-600' : 
                                ($a['status'] == 'in_progress' ? 'text-orange-600' : 'text-gray-600')
                            ?>">
                                <?= ucfirst($a['status']) ?>
                            </td>
                            <td class="px-4 py-2">
                                <form method="POST" class="flex items-center space-x-2">
                                    <input type="hidden" name="assignment_id" value="<?= $a['assignment_id'] ?>">
                                    <input type="hidden" name="request_id" value="<?= $a['request_id'] ?>">
                                    <select name="status" class="border rounded px-2 py-1">
                                        <option value="pending" <?= $a['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="in_progress" <?= $a['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                        <option value="completed" <?= $a['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                        <option value="cancelled" <?= $a['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status"
                                            class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                        Update
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
