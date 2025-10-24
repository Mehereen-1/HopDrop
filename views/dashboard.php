<?php
session_start();
include "../config/db.php";
include "../includes/run_query.php";

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$name = htmlspecialchars($user['name']);
$role = htmlspecialchars($user['role']);
$city = htmlspecialchars($user['city']);
?>

<?php include '../includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HopDrop Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

  <!-- Main Content -->
  <main class="max-w-6xl mx-auto mt-8 px-4">
    <div class="bg-white p-8 rounded-2xl shadow-md">
      <h2 class="text-xl font-bold mb-4 text-gray-700">Welcome back, <?= $name ?> 👋</h2>
      <p class="text-gray-600 mb-6">City: <span class="font-medium"><?= $city ?></span></p>

      <!-- Role-based Dashboard Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <?php if ($role === 'customer'): ?>
          <a href="create_request.php" class="block p-6 bg-blue-50 hover:bg-blue-100 rounded-xl shadow transition">
            <h3 class="font-semibold text-lg text-blue-700 mb-2">📦 Create Delivery Request</h3>
            <p class="text-gray-600 text-sm">Send a package using HopDrop.</p>
          </a>

          <a href="my_requests.php" class="block p-6 bg-blue-50 hover:bg-blue-100 rounded-xl shadow transition">
            <h3 class="font-semibold text-lg text-blue-700 mb-2">📜 View My Requests</h3>
            <p class="text-gray-600 text-sm">Track your current and past deliveries.</p>
          </a>
        <?php endif; ?>

        <?php if ($role === 'deliveryman' || $role === 'volunteer'): ?>
          <a href="available_requests.php" class="block p-6 bg-green-50 hover:bg-green-100 rounded-xl shadow transition">
            <h3 class="font-semibold text-lg text-green-700 mb-2">🚚 Available Deliveries</h3>
            <p class="text-gray-600 text-sm">Accept or view pending delivery requests.</p>
          </a>

          <a href="my_assignments.php" class="block p-6 bg-green-50 hover:bg-green-100 rounded-xl shadow transition">
            <h3 class="font-semibold text-lg text-green-700 mb-2">📦 My Assignments</h3>
            <p class="text-gray-600 text-sm">Manage your ongoing and completed tasks.</p>
          </a>
        <?php endif; ?>

        <a href="ratings.php" class="block p-6 bg-yellow-50 hover:bg-yellow-100 rounded-xl shadow transition">
          <h3 class="font-semibold text-lg text-yellow-700 mb-2">⭐ Ratings & Feedback</h3>
          <p class="text-gray-600 text-sm">View or give feedback for completed deliveries.</p>
        </a>
      </div>
    </div>
  </main>

  <footer class="text-center py-4 text-gray-500 mt-8">
    &copy; <?= date("Y") ?> HopDrop — All Rights Reserved
  </footer>

</body>
</html>
