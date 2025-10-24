<?php
include "../config/db.php";
include "load_queries.php";

$queries = loadQueries(__DIR__ . "/../sql/queries.sql");

/**
 * Executes a named query with optional parameters
 */
function executeQuery($conn, $queries, $query_name, $params = []) {
    if (!isset($queries[$query_name])) {
        die("Query '$query_name' not found!");
    }

    $sql = $queries[$query_name];

    // Replace placeholders with mysqli real_escape_string values
    foreach ($params as $key => $value) {
        $escaped = $conn->real_escape_string($value);
        $sql = str_replace(":$key", "'$escaped'", $sql);
    }

    if ($conn->query($sql) === TRUE) {
        return TRUE;
    } elseif ($result = $conn->query($sql)) {
        // SELECT query
        $rows = [];
        while ($row = $result->fetch_assoc()) $rows[] = $row;
        return $rows;
    } else {
        die("SQL Error: " . $conn->error);
    }
}
?>
