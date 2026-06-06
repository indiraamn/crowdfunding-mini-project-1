<?php
session_start();
require 'koneksi.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST["email"];
    $password = $_POST["password"];
    $role     = $_POST["role"] ?? "donatur";

    if ($role === "penyelenggara") {
        // ---- LOGIN PENYELENGGARA ----
        $stmt = $conn->prepare("SELECT id, nama_penyelenggara, email, password FROM penyelenggara WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user["password"])) {
                $_SESSION["penyelenggara_id"]    = $user["id"];
                $_SESSION["penyelenggara_nama"]  = $user["nama_penyelenggara"];
                $_SESSION["penyelenggara_email"] = $user["email"];
                $_SESSION["role"]                = "penyelenggara";
                header("Location: kelola_kampanye.php");
                exit;
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Email tidak ditemukan.";
        }

    } else {
        // ---- LOGIN DONATUR ----
        $stmt = $conn->prepare("SELECT id, nama, email, password FROM donatur WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user["password"])) {
                $_SESSION["donatur_id"]    = $user["id"];
                $_SESSION["donatur_nama"]  = $user["nama"];
                $_SESSION["donatur_email"] = $user["email"];
                $_SESSION["role"]          = "donatur";
                header("Location: index.php");
                exit;
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Email tidak ditemukan.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BantuSesama</title>
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
            <h2>Login</h2>
            <p>Masukkan akun Anda untuk masuk</p>
            <?php if ($error != ""): ?>
                <p style="color:red; text-align:center;"><?php echo $error; ?></p>
            <?php endif; ?>
 
            <form method="POST" action="">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="email@contoh.com" required>
                </div>
 
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>
 
                <div class="form-group">
                    <label>Masuk sebagai</label>
                    <select name="role">
                        <option value="donatur">Donatur</option>
                        <option value="penyelenggara">Pengelola Kampanye</option>
                    </select>
                </div>
 
                <button type="submit" class="btn-submit">Masuk</button>
            </form>
 
            <p style="text-align:center; margin-top:15px; font-size:0.9rem;">
                Belum punya akun? <a href="register.php" style="color:#1976D2; font-weight:bold;">Daftar di sini</a>
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