<?php
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    // Fetch current password from 'users' table
    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($stored_password);
        $stmt->fetch();
        $stmt->close();

        // Compare current password
        if ($stored_password === $current_password) {  // change to password_verify() if using hashing
            $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $update->bind_param("ss", $new_password, $email);
            
            if ($update->execute()) {
                echo "success";
            } else {
                echo "update_failed";
            }
            $update->close();
        } else {
            echo "incorrect_password";
        }
    } else {
        echo "user_not_found";
    }

    $conn->close();
}
?>
