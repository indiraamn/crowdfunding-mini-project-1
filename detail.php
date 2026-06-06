<?php 
session_start();
require 'koneksi.php'; 
?>
<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$query = "
    SELECT k.*, p.nama_penyelenggara
    FROM kampanye k
    JOIN penyelenggara p ON k.penyelenggara_id = p.id
    WHERE k.id = $id
";
$result  = mysqli_query($conn, $query);
$kampanye = mysqli_fetch_assoc($result);

if (!$kampanye) {
    header('Location: index.php');
    exit;
}

// Hitung donatur verified dari database
$query_donatur = "
    SELECT COUNT(*) as total_donatur
    FROM donasi
    WHERE kampanye_id = $id AND status = 'verified'
";
$result_donatur = mysqli_query($conn, $query_donatur);
$row_donatur    = mysqli_fetch_assoc($result_donatur);
$total_donatur  = (int)$row_donatur['total_donatur'];

$target    = (float)$kampanye['target_dana'];
$terkumpul = (float)$kampanye['dana_terkumpul'];
$persen    = ($target > 0) ? min(100, round(($terkumpul / $target) * 100)) : 0;

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function formatTanggal($tanggal) {
    $bulan = [
        '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
        '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
        '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
    ];
    list($y, $m, $d) = explode('-', $tanggal);
    return "$d {$bulan[$m]} $y";
}

// Cek file gambar, fallback kalau tidak ada
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
                        <li><a href="riwayat_donasi.php">Riwayat Donasi</a></li>
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

    <!-- ===== DETAIL KAMPANYE ===== -->
    <main class="container detail-container">

        <!-- kolom kiri -->
        <section class="detail-left">
            <img
                src="<?= htmlspecialchars($gambar_src) ?>"
                alt="<?= htmlspecialchars($kampanye['judul']) ?>"
            >

            <h2><?= htmlspecialchars($kampanye['judul']) ?></h2>
            <p class="organizer">Oleh: <?= htmlspecialchars($kampanye['nama_penyelenggara']) ?></p>

            <p><strong>Lokasi:</strong> <?= htmlspecialchars($kampanye['lokasi']) ?></p>
            <p><strong>Kategori:</strong> <?= htmlspecialchars($kampanye['kategori']) ?></p>

            <p class="description"><?= nl2br(htmlspecialchars($kampanye['deskripsi'])) ?></p>
        </section>

        <!-- kolom kanan -->
        <aside class="detail-right">
            <div class="donation-box">

                <!-- kelompok 1 -->
                <h3 class="donation-title">Informasi Donasi</h3>

                <!-- kelompok 2 -->
                <div class="donation-main">
                    <p>Target: <strong><?= formatRupiah($target) ?></strong></p>
                    <p>Terkumpul: <strong><?= formatRupiah($terkumpul) ?></strong></p>

                    <div class="progress-bar">
                        <div class="progress" style="width: <?= $persen ?>%;"></div>
                    </div>

                    <p class="percent"><?= $persen ?>% tercapai</p>
                    <p><strong><?= $total_donatur ?></strong> donatur telah berpartisipasi</p>
                    <p class="deadline">Batas Waktu: <?= formatTanggal($kampanye['batas_waktu']) ?></p>
                </div>

                <!-- kelompok 3 -->
                <div class="donation-method">
                    <p><strong>Metode Donasi:</strong></p>
                    <p><?= nl2br(htmlspecialchars($kampanye['rekening_info'])) ?></p>
                </div>

                <!-- button -->
                <div class="donation-action">
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
