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
<body class="bg-gray-50 min-h-screen">

  <!-- Hero Section -->

      <!-- Project Info -->

        <section class="bg-gradient-to-r from-blue-500 to-green-500 text-white py-24">
            <div class="max-w-6xl mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4">
                Welcome, <?= $name ?>!
            </h1>

            <?php if ($role === 'customer'): ?>
                <p class="text-xl md:text-2xl mb-4">
                    Send your packages quickly and safely across the city 📦
                </p>
                <p class="max-w-3xl mx-auto text-white/90 mb-6 text-sm md:text-base">
                    HopDrop connects customers and delivery personnel. As a customer, you can create and track deliveries. 
                    Switch to delivery mode to start earning or volunteer to help your community!
                </p>
                <a href="toggle_role.php" class="inline-block bg-white text-green-600 font-semibold px-6 py-3 rounded-full shadow-lg hover:scale-105 transition transform">
                    Toggle Volunteer / Paid Deliveryman
                </a>

            <?php elseif ($role === 'deliveryman'): ?>
                <p class="text-xl md:text-2xl mb-4">
                    🚚 You are acting as a Paid Deliveryman — earn while you go!
                </p>
                <p class="max-w-3xl mx-auto text-white/90 mb-6 text-sm md:text-base">
                    You can now view available deliveries and start earning money. 
                    Click below to switch to volunteer mode or return to normal mode.
                </p>
                <a href="toggle_role.php" class="inline-block bg-white text-green-600 font-semibold px-6 py-3 rounded-full shadow-lg hover:scale-105 transition transform">
                    Toggle Customer / Volunteer
                </a>

            <?php elseif ($role === 'volunteer'): ?>
                <p class="text-xl md:text-2xl mb-4">
                    🤝 You are acting as a Volunteer — help your community!
                </p>
                <p class="max-w-3xl mx-auto text-white/90 mb-6 text-sm md:text-base">
                    You can now accept deliveries without payment. Thank you for contributing! 
                    Click below to switch to paid delivery mode or return to normal mode.
                </p>
                <a href="toggle_role.php" class="inline-block bg-white text-green-600 font-semibold px-6 py-3 rounded-full shadow-lg hover:scale-105 transition transform">
                    Toggle Paid Deliveryman / Customer
                </a>
            <?php endif; ?>
        </div>
    </section>

  <!-- Main Content -->
  <main class="max-w-6xl mx-auto mt-12 px-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

      <?php if ($role === 'customer'): ?>
        <a href="create_request.php" class="block p-6 bg-blue-50 hover:bg-blue-100 rounded-2xl shadow-md transition transform hover:scale-105">
          <h3 class="font-bold text-lg text-blue-700 mb-2">📦 Create Delivery Request</h3>
          <p class="text-gray-600 text-sm">Send a package using HopDrop.</p>
        </a>

        <a href="my_requests.php" class="block p-6 bg-blue-50 hover:bg-blue-100 rounded-2xl shadow-md transition transform hover:scale-105">
          <h3 class="font-bold text-lg text-blue-700 mb-2">📜 View My Requests</h3>
          <p class="text-gray-600 text-sm">Track your current and past deliveries.</p>
        </a>
      <?php endif; ?>

      <?php if ($role === 'deliveryman' || $role === 'volunteer'): ?>
        <a href="available_deliveries.php" class="block p-6 bg-green-50 hover:bg-green-100 rounded-2xl shadow-md transition transform hover:scale-105">
          <h3 class="font-bold text-lg text-green-700 mb-2">🚚 Available Deliveries</h3>
          <p class="text-gray-600 text-sm">Accept or view pending delivery requests.</p>
        </a>

        <a href="my_assignments.php" class="block p-6 bg-green-50 hover:bg-green-100 rounded-2xl shadow-md transition transform hover:scale-105">
          <h3 class="font-bold text-lg text-green-700 mb-2">📦 My Assignments</h3>
          <p class="text-gray-600 text-sm">Manage your ongoing and completed tasks.</p>
        </a>
      <?php endif; ?>

      <a href="rating_feed.php" class="block p-6 bg-yellow-50 hover:bg-yellow-100 rounded-2xl shadow-md transition transform hover:scale-105">
        <h3 class="font-bold text-lg text-yellow-700 mb-2">⭐ Ratings & Feedback</h3>
        <p class="text-gray-600 text-sm">View or give feedback for completed deliveries.</p>
      </a>
    </div>
  </main>

  <!-- Footer -->
  <footer class="text-center py-6 mt-16 text-gray-500">
    &copy; <?= date("Y") ?> HopDrop — All Rights Reserved
  </footer>

</body>
</html>
