<?php require 'koneksi.php'; ?>
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
                    <li><a href="login.php">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- ===== DETAIL KAMPANYE ===== -->
    <main class="container detail-container">

        <!-- KOLOM KIRI -->
        <section class="detail-left">
            <img
                src="images/<?= htmlspecialchars($kampanye['gambar']) ?>"
                alt="<?= htmlspecialchars($kampanye['judul']) ?>"
                onerror="this.src='images/Gempa Manado.jpeg'"
            >

            <h2><?= htmlspecialchars($kampanye['judul']) ?></h2>
            <p class="organizer">Oleh: <?= htmlspecialchars($kampanye['nama_penyelenggara']) ?></p>

            <p><strong>Lokasi:</strong> <?= htmlspecialchars($kampanye['lokasi']) ?></p>
            <p><strong>Kategori:</strong> <?= htmlspecialchars($kampanye['kategori']) ?></p>

            <p class="description"><?= nl2br(htmlspecialchars($kampanye['deskripsi'])) ?></p>
        </section>

        <!-- KOLOM KANAN -->
        <aside class="detail-right">
            <div class="donation-box">

                <!-- KELOMPOK 1 -->
                <h3 class="donation-title">Informasi Donasi</h3>

                <!-- KELOMPOK 2 -->
                <div class="donation-main">
                    <p>Target: <strong><?= formatRupiah($target) ?></strong></p>
                    <p>Terkumpul: <strong><?= formatRupiah($terkumpul) ?></strong></p>

                    <div class="progress-bar">
                        <div class="progress" style="width: <?= $persen ?>%;"></div>
                    </div>

                    <p class="percent"><?= $persen ?>% tercapai</p>
                    <p class="deadline">Batas Waktu: <?= formatTanggal($kampanye['batas_waktu']) ?></p>
                </div>

                <!-- KELOMPOK 3 -->
                <div class="donation-method">
                    <p><strong>Metode Donasi:</strong></p>
                    <p><?= nl2br(htmlspecialchars($kampanye['rekening_info'])) ?></p>
                </div>

                <!-- BUTTON -->
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