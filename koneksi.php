<?php
$host   = "127.0.0.1";
$dbname = "crowdfunding";
$user   = "root";
$pass   = "";
$port   = 3307;

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>