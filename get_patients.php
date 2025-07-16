<?php
header('Content-Type: application/json');

// Database connection
$conn = new mysqli("localhost", "root", "", "medivault");

// Check connection
if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed"
    ]);
    exit;
}

// Fetch only users with role 'patient'
$sql = "SELECT email FROM users WHERE role = 'patient'";
$result = $conn->query($sql);

$patients = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = ["email" => $row["email"]];
    }

    echo json_encode([
        "status" => "success",
        "patients" => $patients
    ]);
} else {
    echo json_encode([
        "status" => "empty",
        "patients" => []
    ]);
}

$conn->close();
?>
