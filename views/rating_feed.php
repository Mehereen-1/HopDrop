<?php
session_start();
include "../config/db.php";
include "../includes/run_query.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$queries = loadQueries(__DIR__ . "/../sql/queries.sql");

// Fetch all ratings
$ratings = executeQuery($conn, $queries, "select_all_ratings");

// Fetch top trending users
$trending_users = executeQuery($conn, $queries, "select_trending_users");

?>

<?php include '../includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ratings Feed - HopDrop</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 pt-24">

<div class="max-w-6xl mx-auto px-4">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-blue-600">🌟 Ratings Feed</h2>
        <a href="dashboard.php" class="text-blue-600 hover:underline">⬅ Back to Dashboard</a>
    </div>

    <!-- Trending Users -->
    <?php if (!empty($trending_users)): ?>
        <div class="mb-6">
            <h3 class="text-xl font-semibold text-gray-700 mb-2">🔥 Trending Users</h3>
            <div class="flex space-x-4 overflow-x-auto pb-2">
                <?php foreach ($trending_users as $u): ?>
                    <div class="bg-white p-4 rounded-xl shadow-md min-w-[200px]">
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($u['name']) ?></p>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars($u['city']) ?></p>
                        <p class="text-yellow-500 font-semibold">
                            ⭐ <?= round($u['avg_rating'], 1) ?>/5
                        </p>
                        <p class="text-xs text-gray-400">Ratings: <?= $u['rating_count'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Ratings Feed
    <?php if (empty($ratings)): ?>
        <p class="text-gray-500 text-center">No ratings yet.</p>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($ratings as $r): ?>
                <div class="bg-white p-4 rounded-2xl shadow-md flex justify-between items-center">
                    <div>
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($r['rated_user_name']) ?></p>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars($r['rated_user_city']) ?></p>
                        <p class="text-yellow-500 font-semibold">
                            <?= str_repeat('⭐', intval($r['rating'])) ?>
                            <?= intval($r['rating']) < 5 ? str_repeat('☆', 5 - intval($r['rating'])) : '' ?>
                        </p>
                    </div>
                    <div class="text-sm text-gray-400">
                        <?= date("M d, Y", strtotime($r['created_at'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?> -->

    <!-- Ratings Feed Section -->
<div class="max-w-6xl mx-auto mt-8 p-6">
    <h2 class="text-2xl font-bold text-blue-600 mb-6 text-center">⭐ Ratings Feed</h2>

    <?php if (empty($ratings)): ?>
        <p class="text-gray-500 text-center">No ratings yet. Be the first to rate a delivery!</p>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Column 1: Recent Ratings -->
            <div>
                <h3 class="font-semibold text-lg text-gray-700 mb-4">Recent Ratings</h3>
                <div class="space-y-4">
                    <?php foreach ($ratings as $r): ?>
                        <div class="bg-white p-4 rounded-2xl shadow hover:shadow-lg transition flex justify-between items-center">
                            <div>
                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($r['rated_user_name']) ?></p>
                                <p class="text-sm text-gray-500"><?= htmlspecialchars($r['rated_user_city']) ?></p>
                                <p class="text-yellow-500 font-semibold">
                                    <?= str_repeat('⭐', intval($r['rating'])) ?>
                                    <?= intval($r['rating']) < 5 ? str_repeat('☆', 5 - intval($r['rating'])) : '' ?>
                                </p>
                            </div>
                            <div class="text-xs text-gray-400">
                                <?= date("M d, Y", strtotime($r['created_at'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Column 2: Top Rated Users (Trending) -->
            <div>
                <h3 class="font-semibold text-lg text-gray-700 mb-4">Top Rated Deliverymen</h3>
                <?php
                // Example: prepare trending array
                $trending = []; // In real code, calculate avg ratings per user
                foreach ($ratings as $r) {
                    $uid = $r['rated_user'];
                    if (!isset($trending[$uid])) {
                        $trending[$uid] = ['name'=>$r['rated_user_name'], 'city'=>$r['rated_user_city'], 'sum'=>0,'count'=>0];
                    }
                    $trending[$uid]['sum'] += intval($r['rating']);
                    $trending[$uid]['count'] += 1;
                }
                // Sort by average rating
                uasort($trending, fn($a,$b)=>($b['sum']/$b['count'])<=>($a['sum']/$a['count']));
                $trendingTop = array_slice($trending,0,5);
                ?>
                <div class="space-y-4">
                    <?php foreach ($trendingTop as $t): 
                        $avg = round($t['sum']/$t['count'],1);
                    ?>
                        <div class="bg-white p-4 rounded-2xl shadow hover:shadow-lg transition">
                            <p class="font-semibold text-gray-800"><?= htmlspecialchars($t['name']) ?></p>
                            <p class="text-sm text-gray-500"><?= htmlspecialchars($t['city']) ?></p>
                            <p class="text-yellow-500 font-semibold">
                                <?= str_repeat('⭐', floor($avg)) ?><?= floor($avg) < 5 ? str_repeat('☆', 5-floor($avg)) : '' ?> 
                                <span class="text-gray-400 text-sm">(<?= $avg ?>/5)</span>
                            </p>
                            <p class="text-xs text-gray-400"><?= $t['count'] ?> ratings</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Column 3: City-wise Statistics -->
            <div>
                <h3 class="font-semibold text-lg text-gray-700 mb-4">City Statistics</h3>
                <?php
                $cities = [];
                foreach ($ratings as $r) {
                    $city = $r['rated_user_city'] ?? 'Unknown';
                    if (!isset($cities[$city])) $cities[$city] = ['sum'=>0,'count'=>0];
                    $cities[$city]['sum'] += intval($r['rating']);
                    $cities[$city]['count'] += 1;
                }
                ?>
                <div class="space-y-4">
                    <?php foreach ($cities as $cityName => $data): 
                        $avg = round($data['sum']/$data['count'],1);
                    ?>
                        <div class="bg-white p-4 rounded-2xl shadow hover:shadow-lg transition flex justify-between items-center">
                            <p class="font-semibold text-gray-800"><?= htmlspecialchars($cityName) ?></p>
                            <p class="text-yellow-500 font-semibold">
                                <?= str_repeat('⭐', floor($avg)) ?><?= floor($avg) < 5 ? str_repeat('☆', 5-floor($avg)) : '' ?> 
                                <span class="text-gray-400 text-sm">(<?= $avg ?>/5)</span>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    <?php endif; ?>
</div>


</div>
</body>
</html>
