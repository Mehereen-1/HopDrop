<?php
include "../config/db.php";
include "../includes/load_queries.php";

$queries = loadQueries(__DIR__ . "/../sql/queries.sql");

// Choose query name
$query_name = "alter_deliveryrequests_receiver";

if (!isset($queries[$query_name])) {
    die("❌ Query '$query_name' not found in queries.sql");
}

$sql = $queries[$query_name];

if ($conn->multi_query($sql) === TRUE) {
    echo "✅ Migration successful: fk dropped!";
} else {
    echo "❌ Migration failed: " . $conn->error;
}
?>
