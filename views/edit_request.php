<?php
session_start();
include "../config/db.php";
include "../includes/run_query.php";

// Ensure user is logged in as customer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$queries = loadQueries(__DIR__ . "/../sql/queries.sql");
$message = "";

// Get request ID
$request_id = $_GET['id'] ?? null;
if (!$request_id) die("❌ Request ID missing");

// Fetch request
$request = executeQuery($conn, $queries, "select_delivery_request_by_id", ["request_id" => $request_id]);
if (empty($request) || $request[0]['sender_id'] != $_SESSION['user']['user_id']) {
    die("❌ Unauthorized");
}
$request = $request[0];

// Fetch cities for dropdown
$cities = [];
$result = $conn->query("SELECT city_id, name FROM Cities ORDER BY name");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cities[] = $row;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $params = [
        "request_id" => $request_id,
        "pickup_address" => $_POST['pickup_address'] ?? '',
        "delivery_address" => $_POST['delivery_address'] ?? '',
        "city_id" => $_POST['city_id'] ?? null,
        "package_description" => $_POST['package_description'] ?? '',
        "preferred_type" => $_POST['preferred_type'] ?? 'volunteer'
    ];

    $res = executeQuery($conn, $queries, "update_delivery_request", $params);

    if ($res === TRUE) {
        header("Location: my_requests.php?msg=updated");
        exit;
    } else {
        $message = "❌ " . ($res['error'] ?? 'Failed to update request');
    }
}
?>

<?php include '../includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Delivery Request</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen pt-24 flex justify-center items-start">
<div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-lg mt-4">
    <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Edit Delivery Request</h2>

    <?php if ($message): ?>
        <p class="mb-4 text-center <?= strpos($message, '❌') !== false ? 'text-red-600' : 'text-green-600' ?> font-medium"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block mb-1 font-medium">Pickup Address</label>
            <textarea name="pickup_address" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"><?= htmlspecialchars($request['pickup_address']) ?></textarea>
        </div>

        <div>
            <label class="block mb-1 font-medium">Delivery Address</label>
            <textarea name="delivery_address" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"><?= htmlspecialchars($request['delivery_address']) ?></textarea>
        </div>

        <div>
            <label class="block mb-1 font-medium">City</label>
            <select name="city_id" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
                <option value="">Select a City</option>
                <?php foreach ($cities as $city): ?>
                    <option value="<?= $city['city_id'] ?>" <?= ($city['city_id'] == $request['city_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($city['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block mb-1 font-medium">Package Description</label>
            <textarea name="package_description" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"><?= htmlspecialchars($request['package_description']) ?></textarea>
        </div>

        <div>
            <label class="block mb-1 font-medium">Preferred Type</label>
            <select name="preferred_type" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
                <option value="volunteer" <?= ($request['preferred_type'] == 'volunteer') ? 'selected' : '' ?>>Volunteer</option>
                <option value="paid" <?= ($request['preferred_type'] == 'paid') ? 'selected' : '' ?>>Paid</option>
            </select>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Update Request</button>
    </form>

    <p class="mt-4 text-center text-gray-600">
        <a href="my_requests.php" class="text-blue-600 hover:underline">Back to My Requests</a>
    </p>
</div>

</body>
</html>
