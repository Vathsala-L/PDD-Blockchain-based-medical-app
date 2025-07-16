<?php
$conn = new mysqli("localhost", "root", "", "medivault");
$email = $_GET['email'];

$sql = "SELECT filename, cid FROM ipfs_records WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$files = [];
while ($row = $result->fetch_assoc()) {
    $files[] = $row;
}

echo json_encode(["status" => "success", "files" => $files]);
?>
