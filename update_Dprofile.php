<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Create uploads directory if it doesn't exist
$targetDir = "uploads/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"] ?? '';
    $email = $_POST["email"] ?? '';
    $phone = $_POST["phone"] ?? '';
    $specialization = $_POST["specialization"] ?? '';

    if (!$name || !$email || !$phone || !$specialization) {
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

    // Prepare update statement
    if ($profilePhoto) {
        $stmt = $conn->prepare("UPDATE doctor_profile SET name=?, phone=?, specialization=?, profile_photo=? WHERE email=?");
        $stmt->bind_param("sssss", $name, $phone, $specialization, $profilePhoto, $email);
    } else {
        $stmt = $conn->prepare("UPDATE doctor_profile SET name=?, phone=?, specialization=? WHERE email=?");
        $stmt->bind_param("ssss", $name, $phone, $specialization, $email);
    }

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Doctor profile updated successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Update failed."]);
    }

    $stmt->close();
    $conn->close();
}
?>
