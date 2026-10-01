<?php
$host = "localhost";
$username = "root";
$password = "";
$db = "login";

// Create connection
$conn = new mysqli($host, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
    // Para sa production, mas maganda kung walang masyadong detalye ng error ang ipapakita
    die("Failed to connect to the database: " . $conn->connect_error);
}
// Maaari mo ring i-set ang charset para maiwasan ang character issues
$conn->set_charset("utf8mb4");
