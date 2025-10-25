<?php
session_start();
include "../config/db.php";
include "../includes/run_query.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'deliveryman') {
    header("Location: login.php");
    exit;
}

$queries = loadQueries(__DIR__ . "/../sql/queries.sql");

// Fetch assignments for the logged-in deliveryman
$params = ["deliveryman_id" => $_SESSION['user']['user_id']];
$assignments = executeQuery($conn, $queries, "select_assignments_by_deliveryman", $params);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $params = [
        "assignment_id" => $_POST['assignment_id'],
        "latitude" => $_POST['latitude'],
        "longitude" => $_POST['longitude'],
        "route_details" => $_POST['route_details'] ?? null
    ];

    executeQuery($conn, $queries, "insert_route", $params);
    header("Location: routes.php?success=1");
    exit;
}
?>

<?php include "../includes/header.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Route</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY_HERE&libraries=places"></script>
</head>
<body class="bg-gray-100 pt-24">

<div class="max-w-4xl mx-auto bg-white p-6 shadow-md rounded-2xl">
    <h2 class="text-2xl font-bold text-blue-600 mb-6">Add Route</h2>

    <form method="POST">
        <div class="mb-4">
            <label class="block mb-2 font-medium">Select Assignment:</label>
            <select name="assignment_id" required class="w-full border rounded px-3 py-2">
                <option value="">Select Assignment</option>
                <?php foreach ($assignments as $a): ?>
                    <option value="<?= $a['assignment_id'] ?>">
                        Assignment #<?= $a['assignment_id'] ?> – <?= htmlspecialchars($a['pickup_address'] ?? '') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-4">
            <label class="block mb-2 font-medium">Search Location:</label>
            <input id="searchInput" type="text" placeholder="Type a location..." 
                   class="w-full border rounded px-3 py-2">
        </div>

        <div id="map" class="w-full h-80 mb-4 rounded-lg border"></div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block mb-2 font-medium">Latitude:</label>
                <input type="text" id="latitude" name="latitude" readonly class="w-full border rounded px-3 py-2 bg-gray-100">
            </div>
            <div>
                <label class="block mb-2 font-medium">Longitude:</label>
                <input type="text" id="longitude" name="longitude" readonly class="w-full border rounded px-3 py-2 bg-gray-100">
            </div>
        </div>

        <div class="mb-4">
            <label class="block mb-2 font-medium">Route Details (optional):</label>
            <textarea name="route_details" rows="3" class="w-full border rounded px-3 py-2"></textarea>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Save Route
        </button>
    </form>
</div>

<script>
let map, marker, searchBox;

function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 23.8103, lng: 90.4125 }, // Default to Dhaka
        zoom: 12,
    });

    const input = document.getElementById("searchInput");
    const searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    // When a place is selected
    searchBox.addListener("places_changed", function() {
        const places = searchBox.getPlaces();
        if (places.length === 0) return;

        const place = places[0];
        if (!place.geometry) return;

        const location = place.geometry.location;
        map.setCenter(location);
        map.setZoom(15);

        if (marker) marker.setMap(null);
        marker = new google.maps.Marker({
            map,
            position: location,
            draggable: true
        });

        updateLatLng(location.lat(), location.lng());

        google.maps.event.addListener(marker, 'dragend', function(e) {
            updateLatLng(e.latLng.lat(), e.latLng.lng());
        });
    });

    map.addListener("click", (e) => {
        if (marker) marker.setMap(null);
        marker = new google.maps.Marker({
            position: e.latLng,
            map,
            draggable: true
        });
        updateLatLng(e.latLng.lat(), e.latLng.lng());

        google.maps.event.addListener(marker, 'dragend', function(ev) {
            updateLatLng(ev.latLng.lat(), ev.latLng.lng());
        });
    });
}

function updateLatLng(lat, lng) {
    document.getElementById("latitude").value = lat.toFixed(6);
    document.getElementById("longitude").value = lng.toFixed(6);
}

window.onload = initMap;
</script>
</body>
</html>
