<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hopdrop";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if($conn){
    
    //  echo "Connected successfully";
} else {
    echo "Connection failed";
}
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
