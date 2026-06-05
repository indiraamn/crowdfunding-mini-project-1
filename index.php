<?php
session_start();
require 'koneksi.php';
?>
<?php
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

// Ambil input search
$keyword  = isset($_GET['keyword'])  ? trim($_GET['keyword'])  : '';
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$lokasi   = isset($_GET['lokasi'])   ? trim($_GET['lokasi'])   : '';

// Ambil kategori unik untuk dropdown
$res_kat = mysqli_query($conn, "SELECT DISTINCT kategori FROM kampanye ORDER BY kategori ASC");
$daftar_kategori = [];
while ($row = mysqli_fetch_assoc($res_kat)) {
    $daftar_kategori[] = $row['kategori'];
}

// Query kampanye - hanya yang belum melewati batas waktu
$today = date('Y-m-d');
$sql = "
    SELECT k.*, p.nama_penyelenggara
    FROM kampanye k
    JOIN penyelenggara p ON k.penyelenggara_id = p.id
    WHERE k.batas_waktu >= '$today'
";

if ($keyword !== '') {
    $kw = mysqli_real_escape_string($conn, $keyword);
    $sql .= " AND (k.judul LIKE '%$kw%' OR k.deskripsi LIKE '%$kw%')";
}
if ($kategori !== '') {
    $kat = mysqli_real_escape_string($conn, $kategori);
    $sql .= " AND k.kategori = '$kat'";
}
if ($lokasi !== '') {
    $lok = mysqli_real_escape_string($conn, $lokasi);
    $sql .= " AND k.lokasi LIKE '%$lok%'";
}

$sql .= " ORDER BY k.created_at DESC";
$result = mysqli_query($conn, $sql);
$kampanye_list = [];
while ($row = mysqli_fetch_assoc($result)) {
    $kampanye_list[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - BantuSesama</title>
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

    <!-- ===== SEARCH / FILTER ===== -->
    <section class="search-section">
        <div class="container">
            <h2>Temukan Kampanye Donasi</h2>
            <form class="search-form" method="GET" action="index.php">
                <input
                    type="text"
                    name="keyword"
                    placeholder="Judul Kampanye"
                    value="<?= htmlspecialchars($keyword) ?>"
                >
                <select name="kategori">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($daftar_kategori as $kat): ?>
                        <option value="<?= htmlspecialchars($kat) ?>"
                            <?= ($kategori === $kat) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input
                    type="text"
                    name="lokasi"
                    placeholder="Lokasi"
                    value="<?= htmlspecialchars($lokasi) ?>"
                >
                <button type="submit">Cari</button>
            </form>
        </div>
    </section>

    <!-- ===== LIST KAMPANYE ===== -->
    <main class="container">
        <h2 class="section-title">Kampanye Berlangsung Saat Ini</h2>

        <?php if (count($kampanye_list) > 0): ?>
        <div class="campaign-grid">
            <?php foreach ($kampanye_list as $k):
                $target    = (float)$k['target_dana'];
                $terkumpul = (float)$k['dana_terkumpul'];
                $persen    = ($target > 0) ? min(100, round(($terkumpul / $target) * 100)) : 0;
            ?>
            <div class="campaign-card">
                <img
                    src="images/<?= htmlspecialchars($k['gambar']) ?>"
                    alt="<?= htmlspecialchars($k['judul']) ?>"
                    onerror="this.src='images/Gempa Manado.jpeg'"
                >
                <div class="card-content">
                    <span class="category"><?= htmlspecialchars($k['kategori']) ?></span>
                    <h3><?= htmlspecialchars($k['judul']) ?></h3>
                    <p class="organizer">Oleh: <?= htmlspecialchars($k['nama_penyelenggara']) ?></p>
                    <p>Target: <strong><?= formatRupiah($target) ?></strong></p>
                    <p>Terkumpul: <strong><?= formatRupiah($terkumpul) ?></strong></p>
                    <p class="deadline">Batas Waktu: <?= formatTanggal($k['batas_waktu']) ?></p>
                    <a href="detail.php?id=<?= $k['id'] ?>" class="btn-detail">Lihat Detail</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <p style="text-align:center; padding: 40px 0; color: #888;">
                Tidak ada kampanye yang ditemukan.
            </p>
        <?php endif; ?>
    </main>

    <!-- ===== FOOTER ===== -->
    <footer>
        <div class="container">
            <p>&copy; 2026 BantuSesama</p>
        </div>
    </footer>

</body>
</html>