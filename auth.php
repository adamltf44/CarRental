<?php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['full_name'] ?? '');
    $password_input = $_POST['password'] ?? '';

    $sql = "SELECT * FROM users WHERE full_name = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password_input, $user['password'])) {
                $_SESSION['user'] = $user['full_name'];
                $_SESSION['role_id'] = $user['role_id']; 

                if ($user['role_id'] == 1) {
                    header("Location: admin.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                header("Location: index.html?msg=Invalid password");
            }
        } else {
            header("Location: index.html?msg=User not found");
        }
    }
}
exit();