<?php
session_start();
include "../config/db.php";
include "../includes/run_query.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$queries = loadQueries(__DIR__ . "/../sql/queries.sql");
$params = ["user_id" => $_SESSION['user']['user_id']];
$requests = executeQuery($conn, $queries, "select_my_delivery_requests", $params);

// ✅ Ensure requests is array
if (is_string($requests)) {
    $decoded = json_decode($requests, true);
    $requests = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
} elseif (!is_array($requests)) {
    $requests = [];
}

// ✅ Handle rating submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rating'])) {
    $rating = intval($_POST['rating']);
    $feedback = trim($_POST['feedback']);
    $request_id = $_POST['request_id'];

    // ✅ Step 1: Find who delivered this request
    $stmt = $conn->prepare("SELECT deliveryman_id FROM assignments WHERE request_id = ? LIMIT 1");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();


    if (!$row || empty($row['deliveryman_id'])) {
        die("⚠️ No deliveryman found for this request!");
    }

    $rated_user = $row['deliveryman_id']; // this is who we are rating

    // Insert into Ratings table
    $params = [
        "request_id" => $request_id,
        "rated_by" => $_SESSION['user']['user_id'],
        "rated_user" => $rated_user,
        "rating" => $rating,
        "feedback" => $feedback
    ];
    $result = executeQuery($conn, $queries, "insert_rating", $params);

    if ($result !== TRUE) {
        die("Rating failed: " . $result['error'] ?? 'Unknown error');
    }

    echo "<script>alert('Thank you for rating!'); window.location.href='my_requests.php';</script>";
    exit;
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
                        if (is_string($r)) $r = json_decode($r, true);
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

                                <?php if (($r['status'] ?? '') === 'completed' && !empty($r['sender_id'])): ?>
                                    <button 
                                        class="ml-2 bg-yellow-400 hover:bg-yellow-500 text-white px-2 py-1 rounded text-sm"
                                        onclick="openRatingModal('<?= $r['request_id'] ?>', '<?= $r['sender_id'] ?>')">
                                        ⭐ Rate Delivery
                                    </button>
                                <?php elseif (!empty($r['rating'])): ?>
                                    <span class="text-green-600 font-semibold ml-2">Rated ✅</span>
                                <?php endif; ?>
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

<!-- ⭐ Rating Modal -->
<div id="ratingModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
  <div class="bg-white rounded-xl shadow-lg w-96 p-6 relative">
    <h2 class="text-xl font-bold mb-4 text-gray-800">Rate your Delivery</h2>
    <form method="POST">
        <input type="hidden" name="request_id" id="modal_request_id">

        <label class="block mb-2 font-medium">Rating:</label>
        <select name="rating" required class="w-full p-2 border rounded mb-3">
            <option value="">Select</option>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
            <?php endfor; ?>
        </select>

        <label class="block mb-2 font-medium">Feedback (optional):</label>
        <textarea name="feedback" class="w-full border rounded p-2 mb-4" rows="3" placeholder="Write something nice..."></textarea>

        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeRatingModal()" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
            <button type="submit" name="submit_rating" class="bg-yellow-400 text-white px-4 py-2 rounded hover:bg-yellow-500">Submit</button>
        </div>
    </form>
  </div>
</div>

<script>
function openRatingModal(requestId, ratedUserId) {
    document.getElementById('modal_request_id').value = requestId;
    document.getElementById('ratingModal').classList.remove('hidden');
    document.getElementById('ratingModal').classList.add('flex');
}

function closeRatingModal() {
    document.getElementById('ratingModal').classList.add('hidden');
}
</script>

</body>
</html>
