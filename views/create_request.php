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

// Fetch cities from DB
$cities = [];
$result = $conn->query("SELECT city_id, name FROM Cities ORDER BY name");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cities[] = $row;
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $params = [
        "sender_id" => $_SESSION['user']['user_id'],
        "receiver_id" => $_POST['receiver_id'] ?: null,
        "pickup_address" => $_POST['pickup_address'],
        "delivery_address" => $_POST['delivery_address'],
        "city_id" => $_POST['city_id'],
        "package_description" => $_POST['package_description'],
        "preferred_type" => $_POST['preferred_type']
    ];


    $result = executeQuery($conn, $queries, "insert_delivery_request", $params);

    if ($result === TRUE) {
        $message = "✅ Delivery request submitted successfully!";
    } elseif (is_array($result) && isset($result['error'])) {
        $message = "❌ " . $result['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Delivery Request</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-lg">
    <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">New Delivery Request</h2>

    <?php if ($message): ?>
        <p class="mb-4 text-center <?= strpos($message, '✅') !== false ? 'text-green-600' : 'text-red-600' ?> font-medium"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block mb-1 font-medium">Your User ID</label>
            <input type="number" name="sender_id" value="<?= $_SESSION['user']['user_id'] ?>" readonly
                class="w-full border rounded-lg px-3 py-2 bg-gray-100 cursor-not-allowed focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label class="block mb-1 font-medium">Receiver ID <span class="text-gray-500">(Optional)</span></label>
            <input type="number" name="receiver_id" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400" placeholder="Leave empty if not needed">
        </div>

        <div>
            <label class="block mb-1 font-medium">Pickup Address</label>
            <textarea name="pickup_address" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"></textarea>
        </div>

        <div>
            <label class="block mb-1 font-medium">Delivery Address</label>
            <textarea name="delivery_address" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"></textarea>
        </div>

        <div>
            <label class="block mb-1 font-medium">City</label>
            <select name="city_id" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
                <option value="">Select a City</option>
                <?php foreach ($cities as $city): ?>
                    <option value="<?= $city['city_id'] ?>"><?= htmlspecialchars($city['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>


        <div>
            <label class="block mb-1 font-medium">Package Description</label>
            <textarea name="package_description" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400"></textarea>
        </div>

        <div>
            <label class="block mb-1 font-medium">Preferred Type</label>
            <select name="preferred_type" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
                <option value="volunteer">Volunteer</option>
                <option value="paid">Paid</option>
            </select>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Submit Request</button>
    </form>

    <p class="mt-4 text-center text-gray-600">
        <a href="dashboard.php" class="text-blue-600 hover:underline">Back to Dashboard</a>
    </p>
</div>

</body>
</html>
