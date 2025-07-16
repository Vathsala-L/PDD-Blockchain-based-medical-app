<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Pinata JWT
$pinataJWT = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySW5mb3JtYXRpb24iOnsiaWQiOiIxMjJmMjljOS1jMzhlLTRkYmEtOGU0NS1hY2ZlMDQyNzYxYzMiLCJlbWFpbCI6InZhdGNoYWxhbDEwMDMuc3NlQHNhdmVldGhhLmNvbSIsImVtYWlsX3ZlcmlmaWVkIjp0cnVlLCJwaW5fcG9saWN5Ijp7InJlZ2lvbnMiOlt7ImRlc2lyZWRSZXBsaWNhdGlvbkNvdW50IjoxLCJpZCI6IkZSQTEifSx7ImRlc2lyZWRSZXBsaWNhdGlvbkNvdW50IjoxLCJpZCI6Ik5ZQzEifV0sInZlcnNpb24iOjF9LCJtZmFfZW5hYmxlZCI6ZmFsc2UsInN0YXR1cyI6IkFDVElWRSJ9LCJhdXRoZW50aWNhdGlvblR5cGUiOiJzY29wZWRLZXkiLCJzY29wZWRLZXlLZXkiOiI5MTU0MGFiMDRjMmZhNThlNWNiNSIsInNjb3BlZEtleVNlY3JldCI6ImIzM2Y0ZmYwZWIwMjJhMGY5NjM3NDNkMzg2NGZhYmRlZjk4ZWU1MDJiMmM3ODIzY2NiMDQwODdkMWMwZWRlM2EiLCJleHAiOjE3ODAyMTcwNzB9.JzQhS-vJuTi5xtQeorxkVvpKjqd6kORqLSCidfjZtGE'; // replace with your actual JWT

// Connect to database
$conn = new mysqli("localhost", "root", "", "medivault");
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

// Validate request
if (!isset($_POST['email']) || !isset($_FILES['file'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing email or file']);
    exit;
}

$email = $_POST['email'];
$filename = $_FILES['file']['name'];
$fileTmpPath = $_FILES['file']['tmp_name'];

// Check duplicate file by filename + email
$check = $conn->prepare("SELECT * FROM ipfs_uploads WHERE email = ? AND filename = ?");
$check->bind_param("ss", $email, $filename);
$check->execute();
$result = $check->get_result();
if ($result->num_rows > 0) {
    echo json_encode(['status' => 'duplicate', 'message' => 'File already uploaded']);
    exit;
}

// Calculate file hash
$fileContent = file_get_contents($fileTmpPath);
$fileHash = hash('sha256', $fileContent);

// Get previous hash from last upload by this user
$prev = $conn->prepare("SELECT file_hash FROM ipfs_uploads WHERE email = ? ORDER BY id DESC LIMIT 1");
$prev->bind_param("s", $email);
$prev->execute();
$prevResult = $prev->get_result();
$prevHash = ($prevResult->num_rows > 0) ? $prevResult->fetch_assoc()['file_hash'] : '0';

// Upload to Pinata
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.pinata.cloud/pinning/pinFileToIPFS");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $pinataJWT"]);

$postFields = ['file' => new CURLFile($fileTmpPath, mime_content_type($fileTmpPath), $filename)];
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

$response = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($response, true);
if (!isset($responseData['IpfsHash'])) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to upload to IPFS']);
    exit;
}

$cid = $responseData['IpfsHash'];

// Store in DB
$insert = $conn->prepare("INSERT INTO ipfs_uploads (email, filename, cid, file_hash, prev_hash) VALUES (?, ?, ?, ?, ?)");
$insert->bind_param("sssss", $email, $filename, $cid, $fileHash, $prevHash);
if ($insert->execute()) {
    echo json_encode(['status' => 'success', 'cid' => $cid]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save to DB']);
}
?>
