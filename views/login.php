<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include "../config/db.php";
include "../includes/run_query.php";

$queries = loadQueries(__DIR__ . "/../sql/queries.sql");
$message = "";

// Handle login form submit

if (isset($_GET['logout'])){
    echo '<p class="mb-4 text-center text-green-600 font-medium">✅ You have logged out successfully.</p>';
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash password to compare (same as in registration)
    $password_hash = hash('sha256', $password);

    $params = [
        "email" => $email,
        "password_hash" => $password_hash
    ];

    $result = executeQuery($conn, $queries, "login_user", $params);

    if ($result && count($result) === 1) {
        session_start();
        $_SESSION['user'] = $result[0]; // store user data
        header("Location: dashboard.php");
        exit;
    } else {
        $message = "❌ Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HopDrop Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

  <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Login to HopDrop</h2>

    <?php if ($message): ?>
      <p class="mb-4 text-center text-red-500 font-medium"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block mb-1 font-medium">Email</label>
        <input type="email" name="email" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
      </div>

      <div>
        <label class="block mb-1 font-medium">Password</label>
        <input type="password" name="password" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400">
      </div>

      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Login</button>
    </form>

    <p class="mt-4 text-center text-gray-600">
      Don't have an account?
      <a href="register.php" class="text-blue-600 hover:underline">Register</a>
    </p>
  </div>

</body>
</html>
