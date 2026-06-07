<?php
session_start();
require 'koneksi.php';

$error = "";
$success = "";
// proses form hanya jika request method POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
// ambil dan bersihkan input dari form
    $nama = trim($_POST["nama"]);
    $email = trim($_POST["email"]);
    $no_telepon = trim($_POST["no_telepon"]);
    $password = $_POST["password"];

    // validasi : pw minimal 8 karakter
    if (strlen($password) < 8) {
        $error = "Password minimal 8 karakter.";
    } else {
        // Cek email sudah dipakai atau belum
        $cek = $conn->prepare("SELECT id FROM donatur WHERE email = ?");
        $cek->bind_param("s", $email);
        $cek->execute();
        $result = $cek->get_result();

        // email sudah terdaftar, tolak pendaftaran
        if ($result->num_rows > 0) {
            $error = "Email sudah terdaftar.";
        } else {
            // Password harus di-hash supaya cocok dengan password_verify di login.php
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // simpan data donatur ke database
            $stmt = $conn->prepare("INSERT INTO donatur (nama, email, no_telepon, password, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $nama, $email, $no_telepon, $password_hash);

            if ($stmt->execute()) {
                $success = "Pendaftaran berhasil. Silakan login.";
            } else {
                $error = "Pendaftaran gagal.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - BantuSesama</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
 
    <!-- HEADER -->
    <header>
        <div class="container">
            <h1 class="logo">Bantu<span>Sesama</span></h1>
            <nav>
                <ul>
                    <li><a href="index.php">Beranda</a></li>
                    <li><a href="login.php" class="active">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>
 
    <!-- MAIN -->
    <main class="auth-wrapper">
        <section class="auth-box">
            <h2>Daftar Akun</h2>
            <p>Lengkapi data untuk membuat akun baru</p>

            <!-- tampilkan pesan error jika validasi gagal -->
            <?php if ($error != ""): ?>
                <p style="color:red; text-align:center;"><?php echo $error; ?></p>
            <?php endif; ?>
            
            <!-- tampilkan pesan sukses jika pendaftaran berhasil -->
            <?php if ($success != ""): ?>
                <p style="color:green; text-align:center;"><?php echo $success; ?></p>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" placeholder="Nama Lengkap" required>
                </div>
 
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="email@contoh.com" required>
                </div>
 
                <div class="form-group">
                    <label>No. Telepon</label>
                    <input type="tel" name="no_telepon" placeholder="08xxxxxxxxxx" required>
                </div>
 
                <div class="form-group">
                    <label>Password Baru</label>
                    <!-- pw minimal 8 karakter -->
                    <input type="password" name="password" placeholder="Minimal 8 karakter" required>
                </div>
 
                <button type="submit" class="btn-submit">Selesaikan Pendaftaran</button>
            </form>
 
            <p style="text-align:center; margin-top:15px; font-size:0.9rem;">
                Sudah punya akun? <a href="login.php" style="color:#1976D2; font-weight:bold;">Login di sini</a>
            </p>
        </section>
    </main>
 
    <!-- FOOTER -->
    <footer>
        <div class="container">
            <p>&copy; 2026 Sistem Crowdfunding Sosial</p>
        </div>
    </footer>
 
</body>
</html>