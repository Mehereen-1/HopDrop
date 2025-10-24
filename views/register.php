<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../config/db.php";
// include "../includes/load_queries.php";
include "../includes/run_query.php"; // contains executeQuery()

$queries = loadQueries(__DIR__ . "/../sql/queries.sql");

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $params = [
        'name' => $_POST['name'],
        'phone' => $_POST['phone'],
        'email' => $_POST['email'],
        'password' => $_POST['password'],
        'role' => $_POST['role'],
        'address' => $_POST['address'],
        'city' => $_POST['city'],
        'availability_status' => ($_POST['role'] === 'deliveryman' || $_POST['role'] === 'volunteer') ? 'available' : 'unavailable'
    ];

    if (executeQuery($conn, $queries, 'insert_user', $params)) {
        $message = "✅ Registration successful!";
    } else {
        $message = "❌ Error during registration.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>HopDrop | Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-md">
    <h2 class="text-2xl font-semibold text-center text-blue-600 mb-6">HopDrop Registration</h2>

    <?php if ($message): ?>
      <p class="text-center mb-4 <?= str_contains($message, '✅') ? 'text-green-600' : 'text-red-600' ?>">
        <?= $message ?>
      </p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <input type="text" name="name" placeholder="Full Name" required class="w-full border p-2 rounded-lg">
      <input type="text" name="phone" placeholder="Phone" required class="w-full border p-2 rounded-lg">
      <input type="email" name="email" placeholder="Email" required class="w-full border p-2 rounded-lg">
      <input type="password" name="password" placeholder="Password" required class="w-full border p-2 rounded-lg">

      <select name="role" required class="w-full border p-2 rounded-lg">
        <option value="">Select Role</option>
        <option value="customer">Customer</option>
        <option value="deliveryman">Deliveryman</option>
        <option value="volunteer">Volunteer</option>
      </select>

      <textarea name="address" placeholder="Address" class="w-full border p-2 rounded-lg"></textarea>
      <input type="text" name="city" placeholder="City" required class="w-full border p-2 rounded-lg">

      <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-lg font-medium">
        Register
      </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-4">
      Already have an account? <a href="login.php" class="text-blue-600 hover:underline">Login</a>
    </p>
  </div>
</body>
</html>