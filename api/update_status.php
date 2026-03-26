<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'official') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['issue_id']) && isset($_POST['status'])) {
    $issue_id = intval($_POST['issue_id']);
    $status = $conn->real_escape_string($_POST['status']);
    
    $sql = "UPDATE issues SET status = '$status' WHERE id = $issue_id";
    $conn->query($sql);
    
    header("Location: ../dashboard_official.php?msg=Status+Updated");
}
?>
