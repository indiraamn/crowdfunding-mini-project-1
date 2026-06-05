<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION["donatur_id"])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Ambil data kampanye
$query_kampanye = "
    SELECT k.*, p.nama_penyelenggara
    FROM kampanye k
    JOIN penyelenggara p ON k.penyelenggara_id = p.id
    WHERE k.id = $id
";
$result_kampanye = mysqli_query($conn, $query_kampanye);
$kampanye = mysqli_fetch_assoc($result_kampanye);

if (!$kampanye) {
    header("Location: index.php");
    exit;
}

// Ambil data donatur dari session login
$donatur_id = $_SESSION["donatur_id"];

$stmt = $conn->prepare("SELECT id, nama, email FROM donatur WHERE id = ?");
$stmt->bind_param("i", $donatur_id);
$stmt->execute();
$result_donatur = $stmt->get_result();
$donatur = $result_donatur->fetch_assoc();

if (!$donatur) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
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

        <?php if (isset($_SESSION["donatur_id"])): ?>
            <li>
                <span>Halo, <?php echo htmlspecialchars($_SESSION["donatur_nama"]); ?></span>
            </li>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>
        </div>
    </header>

    <!-- MAIN -->
    <main class="container">

        <!-- RINGKASAN -->
        <section class="campaign-summary">
            <h2>Ringkasan Kampanye</h2>
            <p><strong>Judul:</strong><?= htmlspecialchars($kampanye['judul']) ?></p>
            <p><strong>Penyelenggara:</strong><?= htmlspecialchars($kampanye['nama_penyelenggara']) ?></p>
            <p><strong>Target Dana:</strong> <?= formatRupiah($kampanye['target_dana']) ?></p>
            <p><strong>Terkumpul:</strong> <?= formatRupiah($kampanye['dana_terkumpul']) ?></p>
            <p><?= nl2br(htmlspecialchars($kampanye['deskripsi'])) ?></p>
        </section>

        <!-- FORM DONASI -->
        <section class="donation-form">
            <h2>Formulir Donasi</h2>

            <!-- karna harus 4 halaman, act sukses nya disini aja (semoga bisa) -->
            <form action="#sukses">
                
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($donatur['nama'])?>" readonly>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($donatur['email'])?>" readonly>
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