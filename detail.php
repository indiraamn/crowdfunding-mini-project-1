<?php 
session_start();
require 'koneksi.php'; 
?>
<?php

// Ambil id kampanye dari URL, default 1 jika tidak ada
$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Query detail kampanye + nama penyelenggara dari tabel relasi
$query = "
    SELECT k.*, p.nama_penyelenggara
    FROM kampanye k
    JOIN penyelenggara p ON k.penyelenggara_id = p.id
    WHERE k.id = $id
";
$result  = mysqli_query($conn, $query);
$kampanye = mysqli_fetch_assoc($result);

// Kalau id tidak valid / tidak ditemukan, redirect ke beranda
if (!$kampanye) {
    header('Location: index.php');
    exit;
}

// Hitung jumlah donatur yang sudah terverifikasi (status = 'verified')
// Donasi pending/rejected tidak dihitung
$query_donatur = "
    SELECT COUNT(*) as total_donatur
    FROM donasi
    WHERE kampanye_id = $id AND status = 'verified'
";
$result_donatur = mysqli_query($conn, $query_donatur);
$row_donatur    = mysqli_fetch_assoc($result_donatur);
$total_donatur  = (int)$row_donatur['total_donatur'];

// Hitung persentase dana terkumpul untuk progress bar
$target    = (float)$kampanye['target_dana'];
$terkumpul = (float)$kampanye['dana_terkumpul'];
// min(100, ...) memastikan persentase tidak melebihi 100% meski dana melebihi target
$persen    = ($target > 0) ? min(100, round(($terkumpul / $target) * 100)) : 0;

// Fungsi untuk format rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
// Fungsi untuk format tanggal ke format "DD Bulan YYYY"
function formatTanggal($tanggal) {
    $bulan = [
        '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
        '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
        '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
    ];
    list($y, $m, $d) = explode('-', $tanggal);
    return "$d {$bulan[$m]} $y";
}

// Cek apakah file gambar ada di server
$gambar_src = file_exists('images/' . $kampanye['gambar'])
    ? 'images/' . $kampanye['gambar']
    : 'images/Gempa Manado.jpeg';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($kampanye['judul']) ?> - BantuSesama</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- ===== HEADER ===== -->
    <header>
        <div class="container">
            <h1 class="logo">Bantu<span>Sesama</span></h1>
            <nav>
                <ul>
                    <li><a href="index.php">Beranda</a></li>
                    <?php if (isset($_SESSION["donatur_id"])): ?>
                        <!-- Donatur yang login: tampilkan riwayat donasi dan logout -->
                        <li><a href="riwayat_donasi.php">Riwayat Donasi</a></li>
                       <li>
                        <span>Halo, <?php echo htmlspecialchars($_SESSION["donatur_nama"]); ?></span>
                       </li> 
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                      <!-- Belum login: tampilkan tombol Login -->
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- ===== DETAIL KAMPANYE ===== -->
    <main class="container detail-container">

        
        <!-- KOLOM KIRI: gambar dan deskripsi kampanye -->
        <section class="detail-left">
            <img
                src="<?= htmlspecialchars($gambar_src) ?>"
                alt="<?= htmlspecialchars($kampanye['judul']) ?>"
            >

            <h2><?= htmlspecialchars($kampanye['judul']) ?></h2>
            <p class="organizer">Oleh: <?= htmlspecialchars($kampanye['nama_penyelenggara']) ?></p>

            <p><strong>Lokasi:</strong> <?= htmlspecialchars($kampanye['lokasi']) ?></p>
            <p><strong>Kategori:</strong> <?= htmlspecialchars($kampanye['kategori']) ?></p>
            
            <!-- nl2br mengubah newline (\n) menjadi tag <br> supaya paragraf tampil dengan benar -->
            <p class="description"><?= nl2br(htmlspecialchars($kampanye['deskripsi'])) ?></p>
        </section>

         <!-- KOLOM KANAN: info donasi, progress bar, dan tombol donasi -->
        <aside class="detail-right">
            <div class="donation-box">
                <h3 class="donation-title">Informasi Donasi</h3>
                <div class="donation-main">
                    <p>Target: <strong><?= formatRupiah($target) ?></strong></p>
                    <p>Terkumpul: <strong><?= formatRupiah($terkumpul) ?></strong></p>

                     <!-- Progress bar: lebar diatur via inline style berdasarkan persentase -->
                    <div class="progress-bar">
                        <div class="progress" style="width: <?= $persen ?>%;"></div>
                    </div>

                    <p class="percent"><?= $persen ?>% tercapai</p>
                    <!-- Hanya donasi verified yang dihitung sebagai donatur aktif -->
                    <p><strong><?= $total_donatur ?></strong> donatur telah berpartisipasi</p>
                    <p class="deadline">Batas Waktu: <?= formatTanggal($kampanye['batas_waktu']) ?></p>
                </div>

                 <!-- Info rekening/metode pembayaran dari data kampanye -->
                <div class="donation-method">
                    <p><strong>Metode Donasi:</strong></p>
                    <p><?= nl2br(htmlspecialchars($kampanye['rekening_info'])) ?></p>
                </div>

                <div class="donation-action">
                    <!-- Tombol donasi - membawa id kampanye ke halaman donasi -->
                    <!-- Jika belum login, donasi.php akan redirect ke login.php -->
                    <a href="donasi.php?id=<?= $kampanye['id'] ?>" class="btn-donate">Donasi Sekarang</a>
                    <a href="index.php" class="btn-back">← Kembali ke Beranda</a>
                </div>

            </div>
        </aside>

    </main>

    <!-- ===== FOOTER ===== -->
    <footer>
        <div class="container">
            <p>&copy; 2026 BantuSesama</p>
        </div>
    </footer>
</body>
</html>
