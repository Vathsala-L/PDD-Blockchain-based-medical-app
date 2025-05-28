<?php
$host = "localhost";
$user = "root";         // change if needed
$password = "";         // change if needed
$database = "medivault"; // your database name

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
