<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "medivault");

$id = $_POST['id'] ?? null;
$status = $_POST['status'] ?? '';

if ($id && in_array($status, ['approved', 'denied'])) {
    $stmt = $conn->prepare("UPDATE access_requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "updated"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
} else {
    echo json_encode(["status" => "invalid"]);
}
?>
