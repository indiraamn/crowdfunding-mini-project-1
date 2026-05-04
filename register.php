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
                    <li><a href="index.html">Beranda</a></li>
                    <li><a href="login.html">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>
 
    <!-- MAIN -->
    <main class="auth-wrapper">
        <section class="auth-box">
            <h2>Daftar Akun</h2>
            <p>Lengkapi data untuk membuat akun baru</p>
 
            <form>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" placeholder="Nama Lengkap" required>
                </div>
 
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" placeholder="email@contoh.com" required>
                </div>
 
                <div class="form-group">
                    <label>No. Telepon</label>
                    <input type="tel" placeholder="08xxxxxxxxxx" required>
                </div>
 
                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" placeholder="Minimal 8 karakter" required>
                </div>
 
                <a href="login.html" class="btn-submit">Selesaikan Pendaftaran</a>
            </form>
 
            <p style="text-align:center; margin-top:15px; font-size:0.9rem;">
                Sudah punya akun? <a href="login.html" style="color:#1976D2; font-weight:bold;">Login di sini</a>
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