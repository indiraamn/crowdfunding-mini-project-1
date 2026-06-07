<?php
$host   = "127.0.0.1";
$dbname = "crowdfunding";
$user   = "root";
$pass   = "";
$port   = 3306;

$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Cek apakah koneksi berhasil, kalau gagal langsung hentikan program
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset ke utf8mb4 supaya karakter khusus (emoji, dll) bisa tersimpan dengan benar
$conn->set_charset("utf8mb4");
?>