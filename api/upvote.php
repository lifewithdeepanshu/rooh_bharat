<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['issue_id'])) {
    $user_id = $_SESSION['user_id'];
    $issue_id = intval($_POST['issue_id']);
    
    // Check if already upvoted
    $check_sql = "SELECT * FROM upvotes WHERE user_id = $user_id AND issue_id = $issue_id";
    $result = $conn->query($check_sql);
    
    if ($result->num_rows > 0) {
        // Remove upvote
        $delete_sql = "DELETE FROM upvotes WHERE user_id = $user_id AND issue_id = $issue_id";
        if ($conn->query($delete_sql) === TRUE) {
            $update_count = "UPDATE issues SET upvotes_count = upvotes_count - 1 WHERE id = $issue_id";
            $conn->query($update_count);
            
            $get_count = "SELECT upvotes_count FROM issues WHERE id = $issue_id";
            $count_result = $conn->query($get_count);
            $new_count = $count_result->fetch_assoc()['upvotes_count'];
            
            echo json_encode(['success' => true, 'action' => 'removed', 'new_count' => $new_count]);
        }
    } else {
        // Add upvote
        $insert_sql = "INSERT INTO upvotes (user_id, issue_id) VALUES ($user_id, $issue_id)";
        if ($conn->query($insert_sql) === TRUE) {
            $update_count = "UPDATE issues SET upvotes_count = upvotes_count + 1 WHERE id = $issue_id";
            $conn->query($update_count);
            
            $get_count = "SELECT upvotes_count FROM issues WHERE id = $issue_id";
            $count_result = $conn->query($get_count);
            $new_count = $count_result->fetch_assoc()['upvotes_count'];
            
            echo json_encode(['success' => true, 'action' => 'added', 'new_count' => $new_count]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
