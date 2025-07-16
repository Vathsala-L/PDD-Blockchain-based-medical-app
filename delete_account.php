<?php
include "db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $role = $_POST['role'];

    if ($role == 'patient') {
        $stmt = $conn->prepare("DELETE FROM patients WHERE email = ?");
    } else if ($role == 'doctor') {
        $stmt = $conn->prepare("DELETE FROM doctors WHERE email = ?");
    } else {
        echo "invalid role";
        exit;
    }

    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>
