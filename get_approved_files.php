<?php
header('Content-Type: application/json');
include 'db.php';

if (!isset($_GET['doctor_email']) || !isset($_GET['patient_email'])) {
    echo json_encode(["status" => "error", "message" => "Missing doctor or patient email"]);
    exit();
}

$doctorEmail = $_GET['doctor_email'];
$patientEmail = $_GET['patient_email'];

// Fetch approved access requests
$sql = "SELECT filename, cid FROM access_requests 
        WHERE doctor_email = ? AND patient_email = ? AND status = 'approved'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $doctorEmail, $patientEmail);
$stmt->execute();
$result = $stmt->get_result();

$files = [];

while ($row = $result->fetch_assoc()) {
    $files[] = [
        "filename" => $row['filename'],
        "cid" => $row['cid']
    ];
}

if (count($files) > 0) {
    echo json_encode(["status" => "success", "files" => $files]);
} else {
    echo json_encode(["status" => "error", "message" => "No approved files found"]);
}

$stmt->close();
$conn->close();
?>
