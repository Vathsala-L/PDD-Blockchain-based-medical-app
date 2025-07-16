<?php
include 'db.php';

$email = $_GET['email'];

$sql = "SELECT * FROM access_requests WHERE patient_email = ? AND status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$requests = [];
while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}

echo json_encode(["status" => "success", "requests" => $requests]);
?>
