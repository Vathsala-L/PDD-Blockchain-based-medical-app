<?php
include 'db.php';

if (!isset($_GET['patient_email'])) {
    echo json_encode(["error" => "Missing patient_email"]);
    exit();
}

$patient_email = $_GET['patient_email'];

$sql = "SELECT id, doctor_email, patient_email, filename, cid, request_date 
        FROM access_requests 
        WHERE status = 'approved' AND patient_email = ?
        ORDER BY request_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patient_email);
$stmt->execute();
$result = $stmt->get_result();

$approved = [];

while ($row = $result->fetch_assoc()) {
    $approved[] = $row;
}

echo json_encode($approved);

$stmt->close();
$conn->close();
?>
