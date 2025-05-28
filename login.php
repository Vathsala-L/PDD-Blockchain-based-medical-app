<?php
header("Content-Type: application/json");
include 'db.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Missing email or password."]);
    exit;
}

$stmt = $conn->prepare("SELECT role FROM users WHERE email = ? AND password = ?");
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 1) {
    $stmt->bind_result($role);
    $stmt->fetch();
    echo json_encode(["status" => "success", "role" => $role]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid credentials."]);
}

$stmt->close();
$conn->close();
?>
