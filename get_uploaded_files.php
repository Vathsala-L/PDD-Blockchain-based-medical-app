<?php
header('Content-Type: application/json');

// DB connection
$conn = new mysqli("localhost", "root", "", "medivault");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

if (!isset($_GET['email'])) {
    echo json_encode(["status" => "error", "message" => "Missing email parameter"]);
    exit;
}

$email = $_GET['email'];
$stmt = $conn->prepare("SELECT filename, cid FROM ipfs_uploads WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$files = [];
while ($row = $result->fetch_assoc()) {
    $files[] = [
        'filename' => $row['filename'],
        'cid' => $row['cid']
    ];
}

echo json_encode(["status" => "success", "files" => $files]);
?>
