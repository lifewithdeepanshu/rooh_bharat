<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $latitude = floatval($_POST['latitude']);
    $longitude = floatval($_POST['longitude']);
    
    $photo_url = '';
    
    // Handle File Upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_name = time() . '_' . basename($_FILES["photo"]["name"]);
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo_url = 'uploads/' . $file_name;
        }
    }
    
    $sql = "INSERT INTO issues (user_id, title, description, photo_url, latitude, longitude) 
            VALUES ('$user_id', '$title', '$description', '$photo_url', $latitude, $longitude)";
            
    if ($conn->query($sql) === TRUE) {
        // Award 10 points for posting an issue
        $conn->query("UPDATE users SET points = points + 10 WHERE id = $user_id");
        header("Location: ../dashboard_citizen.php?msg=Issue Posted Successfully (+10 points)");
    } else {
        header("Location: ../post_issue.php?error=Failed to post issue");
    }
}
?>
