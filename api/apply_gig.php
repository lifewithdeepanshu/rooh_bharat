<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'youth') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['gig_id'])) {
    $user_id = $_SESSION['user_id'];
    $gig_id = intval($_POST['gig_id']);
    
    $sql = "INSERT INTO gig_applications (gig_id, user_id, status) VALUES ($gig_id, $user_id, 'applied')";
    $conn->query($sql);
    
    header("Location: ../dashboard_youth.php?msg=Application+Submitted");
}
?>
