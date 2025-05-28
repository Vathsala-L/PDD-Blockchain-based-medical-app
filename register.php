<?php
header("Content-Type: application/json");
include 'db.php';

$data = json_decode(file_get_contents("php://input"));

$email = $data->email;
$password = $data->password;
$role = strtolower($data->role); // patient or doctor

// Basic validation
if (!$email || !$password || !$role) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit;
}

// Check if user already exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "User already exists."]);
} else {
    $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $password, $role);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Registration successful."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Registration failed."]);
    }
    $stmt->close();
}
$check->close();
$conn->close();
?>
