<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'login') {
        $mobile = $conn->real_escape_string($_POST['mobile']);
        $password = $_POST['password'];

        $sql = "SELECT id, name, role, password FROM users WHERE mobile = '$mobile'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                
                header("Location: ../dashboard_" . $user['role'] . ".php");
                exit();
            } else {
                header("Location: ../index.php?error=Invalid password");
                exit();
            }
        } else {
            header("Location: ../index.php?error=User not found");
            exit();
        }
    } elseif ($action == 'register') {
        $name = $conn->real_escape_string($_POST['name']);
        $mobile = $conn->real_escape_string($_POST['mobile']);
        // Mock Aadhaar verification -> auto true for now
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $conn->real_escape_string($_POST['role']);

        $sql = "INSERT INTO users (name, mobile, password, role, aadhaar_verified) VALUES ('$name', '$mobile', '$password', '$role', 1)";
        
        if ($conn->query($sql) === TRUE) {
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;
            
            header("Location: ../dashboard_$role.php");
            exit();
        } else {
            header("Location: ../index.php?error=Registration failed or mobile already exists");
            exit();
        }
    }
}
?>
