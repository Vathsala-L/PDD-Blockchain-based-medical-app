<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "medivault");

$doctor_email = $_POST['doctor_email'] ?? '';
$patient_email = $_POST['patient_email'] ?? '';
$filename = $_POST['filename'] ?? '';
$cid = $_POST['cid'] ?? '';
$date = date("Y-m-d");

if (empty($doctor_email) || empty($patient_email) || empty($filename) || empty($cid)) {
    echo json_encode(["status" => "error", "message" => "Missing fields"]);
    exit;
}

// Check for duplicate request
$stmt = $conn->prepare("SELECT * FROM access_requests WHERE doctor_email=? AND patient_email=? AND filename=?");
$stmt->bind_param("sss", $doctor_email, $patient_email, $filename);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "duplicate"]);
    exit;
}

// Insert new request
$stmt = $conn->prepare("INSERT INTO access_requests (doctor_email, patient_email, filename, cid, status, request_date) VALUES (?, ?, ?, ?, 'pending', ?)");
$stmt->bind_param("sssss", $doctor_email, $patient_email, $filename, $cid, $date);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to insert request"]);
}
?>
