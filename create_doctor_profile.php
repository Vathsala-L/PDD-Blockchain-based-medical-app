<?php
header('Content-Type: application/json');
error_reporting(0);

$conn = new mysqli("localhost", "root", "", "medivault");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

$email = $_GET['email'] ?? '';
if (empty($email)) {
    echo json_encode(["error" => "Missing email parameter"]);
    exit();
}

$sql = "SELECT name, email, phone, specialization, profile_photo FROM doctor_profile WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $profile = $result->fetch_assoc();
    echo json_encode($profile);
} else {
    echo json_encode(["error" => "Doctor not found"]);
}

$stmt->close();
$conn->close();
?>
