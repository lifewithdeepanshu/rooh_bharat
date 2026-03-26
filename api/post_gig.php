<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'official') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $reward_points = intval($_POST['reward_points']);
    $reward_money = floatval($_POST['reward_money']);
    
    $sql = "INSERT INTO gigs (title, description, reward_points, reward_money, status) VALUES ('$title', '$description', $reward_points, $reward_money, 'open')";
            
    if ($conn->query($sql) === TRUE) {
        header("Location: ../dashboard_official.php?msg=New Civic Gig Created Successfully");
    } else {
        header("Location: ../dashboard_official.php?msg=Failed to create gig");
    }
} else {
    header("Location: ../dashboard_official.php");
}
?>
