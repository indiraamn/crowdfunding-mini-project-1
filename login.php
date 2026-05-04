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
                    <li><a href="index.html">Beranda</a></li>
                    <li><a href="login.html">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>
 
    <!-- MAIN -->
    <main class="auth-wrapper">
        <section class="auth-box">
            <h2>Login</h2>
            <p>Masukkan akun Anda untuk masuk</p>
 
            <form>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" placeholder="email@contoh.com" required>
                </div>
 
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" placeholder="Masukkan password" required>
                </div>
 
                <div class="form-group">
                    <label>Masuk sebagai</label>
                    <select>
                        <option>Donatur</option>
                        <option>Pengelola Kampanye</option>
                    </select>
                </div>
 
                <a href="index.html" class="btn-submit">Masuk</a>
            </form>
 
            <p style="text-align:center; margin-top:15px; font-size:0.9rem;">
                Belum punya akun? <a href="register.html" style="color:#1976D2; font-weight:bold;">Daftar di sini</a>
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