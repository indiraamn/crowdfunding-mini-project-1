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

$sql .= " ORDER BY k.batas_waktu ASC, k.dana_terkumpul ASC";
$result = mysqli_query($conn, $sql);
$kampanye_all = [];
while ($row = mysqli_fetch_assoc($result)) {
    $kampanye_all[] = $row;
}

// Pagination
$per_page = 6;
$total = count($kampanye_all);
$total_pages = max(1, ceil($total / $per_page));
$page = isset($_GET['page']) ? max(1, min((int)$_GET['page'], $total_pages)) : 1;
$offset = ($page - 1) * $per_page;
$kampanye_list = array_slice($kampanye_all, $offset, $per_page);
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
        <li><a href="index.php" class="active">Beranda</a></li>

        <?php if (isset($_SESSION["donatur_id"])): ?>
             <li><a href="riwayat_donasi.php">Riwayat Donasi</a></li>
            <li>
                <span>Halo, <?php echo htmlspecialchars($_SESSION["donatur_nama"]); ?></span>
            </li>
            <li><a href="logout.php">Logout</a></li>
        <?php elseif (isset($_SESSION["penyelenggara_id"])): ?>
            <li><a href="kelola_kampanye.php">Dashboard</a></li>
            <li>
                <span>Halo, <?php echo htmlspecialchars($_SESSION["penyelenggara_nama"]); ?></span>
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

        <!-- pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php
            $params = $_GET;
            if ($page > 1):
                $params['page'] = $page - 1;
            ?>
                <a href="?<?= http_build_query($params) ?>" class="page-btn">← Prev</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++):
                $params['page'] = $i;
            ?>
                <a href="?<?= http_build_query($params) ?>"
                   class="page-btn <?= $i === $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages):
                $params['page'] = $page + 1;
            ?>
                <a href="?<?= http_build_query($params) ?>" class="page-btn">Next →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

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