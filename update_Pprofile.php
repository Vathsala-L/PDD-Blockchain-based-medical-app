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
        echo json_encode(["status" => "error", "message" => "Missing fields"]);
        exit();
    }

    $profilePhoto = null;
    if (isset($_FILES["profile_photo"])) {
        $fileName = basename($_FILES["profile_photo"]["name"]);
        $filePath = $targetDir . uniqid() . "_" . $fileName;

        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $filePath)) {
            $profilePhoto = $filePath;
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to upload image."]);
            exit();
        }
    }

    $conn = new mysqli("localhost", "root", "", "medivault");

    if ($conn->connect_error) {
        echo json_encode(["status" => "error", "message" => "Database connection failed."]);
        exit();
    }

    if ($profilePhoto) {
        $stmt = $conn->prepare("UPDATE patient_profile SET name=?, phone=?, dob=?, profile_photo=? WHERE email=?");
        $stmt->bind_param("sssss", $name, $phone, $dob, $profilePhoto, $email);
    } else {
        $stmt = $conn->prepare("UPDATE patient_profile SET name=?, phone=?, dob=? WHERE email=?");
        $stmt->bind_param("ssss", $name, $phone, $dob, $email);
    }

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Profile updated successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Update failed."]);
    }

    $stmt->close();
    $conn->close();
}
?>
