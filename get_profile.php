<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "medivault");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

$email = $_GET['email'] ?? '';
$role = $_GET['role'] ?? '';

if (empty($email) || empty($role)) {
    echo json_encode(["error" => "Missing email or role"]);
    exit();
}

if ($role === "doctor") {
    $sql = "SELECT name AS full_name, email, phone AS phone_number, '' AS date_of_birth, profile_photo FROM doctor_profile WHERE email = ?";
} elseif ($role === "patient") {
    $sql = "SELECT name AS full_name, email, phone AS phone_number, dob AS date_of_birth, profile_photo FROM patient_profile WHERE email = ?";
} else {
    echo json_encode(["error" => "Invalid role"]);
    exit();
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $profile = $result->fetch_assoc();

    // Convert photo filename to full URL
    $photoFile = $profile['profile_photo'] ?? '';
    $profile['profile_photo'] = $photoFile ? "http://localhost/MediVault/$photoFile" : "";

    echo json_encode($profile, JSON_PRETTY_PRINT);
} else {
    echo json_encode(["error" => ucfirst($role) . " not found"]);
}

$stmt->close();
$conn->close();
?>
