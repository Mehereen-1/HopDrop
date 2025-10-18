<?php
include "../config/db.php";
include "../includes/load_queries.php";

$queries = loadQueries(__DIR__ . "/queries.sql");

// Choose a query name dynamically (example: from URL)
$name = $_GET['q'] ?? 'select_all_users';

if (!isset($queries[$name])) {
    die("Query '$name' not found!");
}

$sql = $queries[$name];
echo "<h3>Running Query: $name</h3>";
echo "<pre>$sql</pre>";

$result = $conn->query($sql);

if ($result === TRUE) {
    echo "<p>✅ Query executed successfully.</p>";
} elseif ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='6'><tr>";
    while ($field = $result->fetch_field()) {
        echo "<th>{$field->name}</th>";
    }
    echo "</tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $val) echo "<td>$val</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>⚠️ No results or error: {$conn->error}</p>";
}
?>
