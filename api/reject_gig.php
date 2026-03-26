<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'official') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['app_id'])) {
    $app_id = intval($_POST['app_id']);
    
    // Update application status to rejected
    $conn->query("UPDATE gig_applications SET status = 'rejected' WHERE id = $app_id");
    
    header("Location: ../dashboard_official.php?msg=Gig+Application+Rejected");
    exit();
}
header("Location: ../dashboard_official.php");
?>
