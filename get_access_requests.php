<?php
$conn = new mysqli("localhost", "root", "", "medivault");
$email = $_GET['patient_email'];

$sql = "SELECT * FROM access_requests WHERE patient_email = ? AND status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$requests = [];

while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}

echo json_encode($requests);
?>
