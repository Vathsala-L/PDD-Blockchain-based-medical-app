<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$targetDir = "uploads/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"] ?? '';
    $email = $_POST["email"] ?? '';
    $phone = $_POST["phone"] ?? '';
    $dob = $_POST["dob"] ?? '';

    if (!$name || !$email || !$phone || !$dob) {
        echo json_encode(["success" => "0", "message" => "Missing fields"]);
        exit();
    }

    $profilePhoto = "";

    if (isset($_FILES["profile_photo"])) {
        $fileName = basename($_FILES["profile_photo"]["name"]);
        $filePath = $targetDir . uniqid() . "_" . $fileName;

        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $filePath)) {
            $profilePhoto = $filePath;
        }
    }

    $conn = new mysqli("localhost", "root", "", "medivault");

    if ($conn->connect_error) {
        echo json_encode(["success" => "0", "message" => "Database error"]);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO patient_profile (name, email, phone, dob, profile_photo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $dob, $profilePhoto);

    if ($stmt->execute()) {
        echo json_encode(["success" => "1", "message" => "Patient profile saved"]);
    } else {
        echo json_encode(["success" => "0", "message" => "Insert failed: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
