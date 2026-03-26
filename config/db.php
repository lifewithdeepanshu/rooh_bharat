<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "rooh_bharat_db";

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Select DB
$conn->select_db($dbname);
if ($conn->error) {
    die("Database selection failed: " . $conn->error);
}
?>
