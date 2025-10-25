<?php
// Make sure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get role safely
$role = $_SESSION['user']['role'] ?? '';
?>

<nav class="bg-blue-500 text-white p-4 shadow-lg fixed w-full top-0 z-50 rounded-b-2xl">
    <div class="max-w-6xl mx-auto flex justify-between items-center">
        <!-- Logo / Brand -->
        <h1 class="font-extrabold text-2xl tracking-wide">🌸 HopDrop</h1>

        <!-- Navigation Links -->
        <div class="flex space-x-3">
            <a href="../views/dashboard.php"
               class="px-4 py-2 rounded-xl bg-white/20 hover:bg-white/30 transition transform hover:scale-105">
                Dashboard
            </a>

            <?php if ($role === 'customer'): ?>
                <a href="../views/my_requests.php"
                    class="px-4 py-2 rounded-xl bg-white/20 hover:bg-white/30 transition transform hover:scale-105">
                    My Requests
                </a>
            <?php endif; ?>

            <?php if ($role === 'deliveryman' || $role === 'volunteer'): ?>
                <a href="../views/update_route.php"
                   class="px-4 py-2 rounded-xl bg-white/20 hover:bg-white/30 transition transform hover:scale-105">
                    Update Route
                </a>
            <?php endif; ?>

            <?php if ($role === 'customer'): ?>
                <a href="../views/all_packages.php"
                   class="px-4 py-2 rounded-xl bg-white/20 hover:bg-white/30 transition transform hover:scale-105">
                    Your Parcels
                </a>
            <?php endif; ?>

            <a href="../views/logout.php"
               class="px-4 py-2 rounded-xl bg-white/20 hover:bg-white/30 transition transform hover:scale-105">
                Logout
            </a>
        </div>
    </div>
</nav>
