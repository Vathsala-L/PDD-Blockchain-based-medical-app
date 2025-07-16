<?php
include 'db.php';

$email = $_POST['email'];
$role = $_POST['role'];

$table = $role === "doctor" ? "doctor_notifications" : "patient_notifications";

$sql = "SELECT id, message, date, is_read FROM $table WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];

while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode($notifications);
$conn->close();
?>
