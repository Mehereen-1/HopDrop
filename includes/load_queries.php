

<?php
function loadQueries($filePath) {
    $queries = [];
    $contents = file_get_contents($filePath);

    // Match all query names
    if (preg_match_all('/--\s*name:\s*(\w+)/', $contents, $names)) {
        $parts = preg_split('/--\s*name:\s*\w+/', $contents);

        foreach ($names[1] as $index => $name) {
            $queries[$name] = trim($parts[$index + 1] ?? '');
        }
    } else {
        error_log("⚠️ No named queries found in $filePath");
    }

    return $queries;
}
?>