<?php require 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donasi - BantuSesama</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- HEADER -->
    <header>
        <div class="container">
            <div class="logo">
                <h1>Bantu<span>Sesama</span></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Beranda</a></li>
                    <li><a href="login.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- MAIN -->
    <main class="container">

        <!-- RINGKASAN -->
        <section class="campaign-summary">
            <h2>Ringkasan Kampanye</h2>
            <p><strong>Judul:</strong> Bantuan Korban Bencana Alam</p>
            <p><strong>Penyelenggara:</strong> Relawan Peduli Kasih</p>
            <p><strong>Target Dana:</strong> Rp 50.000.000</p>
            <p><strong>Terkumpul:</strong> Rp 15.000.000</p>
            <p>Kampanye ini bertujuan membantu korban bencana alam yang terdampak.</p>
        </section>

        <!-- FORM DONASI -->
        <section class="donation-form">
            <h2>Formulir Donasi</h2>

            <!-- karna harus 4 halaman, act sukses nya disini aja (semoga bisa) -->
            <form action="#sukses">
                
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="nominal">Nominal Donasi</label>
                    <input type="number" id="nominal" name="nominal" required step="5000">
                </div>

                <div class="form-group">
                    <label for="pembayaran">Metode Pembayaran</label>
                    <select id="pembayaran" name="pembayaran" required>
                        <option value="">-- Pilih Metode --</option>
                        <option value="bank">Transfer Bank</option>
                        <option value="ewallet">E-Wallet</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="pesan">Pesan Dukungan</label>
                    <textarea id="pesan" name="pesan" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label for="bukti">Bukti Transfer</label>
                    <input type="file" id="bukti" name="bukti">
                </div>

                <button type="submit" class="btn-submit">Kirim Donasi</button>

            </form>

            <!-- pesan berhasil -->
            <p id="sukses" class="success-msg">
                Berhasil mengirimkan donasi, terimakasih atas dukungan Anda!
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