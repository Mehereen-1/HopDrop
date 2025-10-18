<?php
require_once __DIR__ . '/../config/db.php';

$sqlFiles = glob(__DIR__ . '/*.sql');
sort($sqlFiles);

echo "<h3>🚀 Running HopDrop SQL setup...</h3><hr>";

foreach ($sqlFiles as $file) {
    $query = file_get_contents($file);
    echo "<strong>Executing:</strong> " . basename($file) . "<br>";

    if ($conn->multi_query($query)) {
        do { $conn->store_result(); } while ($conn->next_result());
        echo "✅ Success<br><br>";
    } else {
        echo "❌ Error: " . $conn->error . "<br><br>";
    }
}

echo "<hr><h4>✅ All tables created successfully!</h4>";
$conn->close();
