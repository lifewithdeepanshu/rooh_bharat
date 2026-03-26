<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'official') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['app_id'])) {
    $app_id = intval($_POST['app_id']);
    
    // Get application and gig details
    $sql = "SELECT ga.user_id as youth_id, g.reward_points 
            FROM gig_applications ga 
            JOIN gigs g ON ga.gig_id = g.id 
            WHERE ga.id = $app_id AND ga.status != 'completed'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows == 1) {
        $data = $result->fetch_assoc();
        $youth_id = $data['youth_id'];
        $reward_points = $data['reward_points'];
        
        // Update application status
        $conn->query("UPDATE gig_applications SET status = 'completed' WHERE id = $app_id");
        
        // Award points to youth
        $conn->query("UPDATE users SET points = points + $reward_points WHERE id = $youth_id");
        
        header("Location: ../dashboard_official.php?msg=Gig+Completed+and+Points+Awarded");
        exit();
    }
}
header("Location: ../dashboard_official.php");
?>
