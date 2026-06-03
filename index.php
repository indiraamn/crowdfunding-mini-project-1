<?php require 'koneksi.php'; ?>
<?php
// ===== FUNGSI HELPER =====
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function formatTanggal($tanggal) {
    $bulan = [
        '01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr',
        '05'=>'Mei','06'=>'Jun','07'=>'Jul','08'=>'Agu',
        '09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'
    ];
    list($y, $m, $d) = explode('-', $tanggal);
    return "$d {$bulan[$m]} $y";
}

// ===== AMBIL INPUT SEARCH =====
$keyword  = isset($_GET['keyword'])  ? trim($_GET['keyword'])  : '';
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$lokasi   = isset($_GET['lokasi'])   ? trim($_GET['lokasi'])   : '';

// ===== AMBIL DAFTAR KATEGORI UNIK UNTUK DROPDOWN =====
$res_kat = mysqli_query($conn, "SELECT DISTINCT kategori FROM kampanye ORDER BY kategori ASC");
$daftar_kategori = [];
while ($row = mysqli_fetch_assoc($res_kat)) {
    $daftar_kategori[] = $row['kategori'];
}

// ===== QUERY KAMPANYE DINAMIS =====
// Tampilkan hanya kampanye yang belum selesai (batas_waktu >= hari ini)
$today = date('Y-m-d');

$sql = "
    SELECT k.*, p.nama_penyelenggara
    FROM kampanye k
    JOIN penyelenggara p ON k.penyelenggara_id = p.id
<<<<<<< Updated upstream
    WHERE k.batas_waktu >= '$today'
=======
    WHERE k.status = 'aktif' AND k.batas_waktu >= '$today'
>>>>>>> Stashed changes
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

$result     = mysqli_query($conn, $sql);
$kampanye_list = [];
while ($row = mysqli_fetch_assoc($result)) {
    $kampanye_list[] = $row;
}

$jumlah = count($kampanye_list);
$ada_filter = ($keyword !== '' || $kategori !== '' || $lokasi !== '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - BantuSesama</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* ===== INDEX PAGE EXTRA STYLES ===== */

        /* Hero Banner */
        .hero {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 60%, #3b82f6 100%);
            color: #fff;
            padding: 52px 0 48px;
            text-align: center;
        }
        .hero h2 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 10px;
        }
        .hero p {
            font-size: 1.05rem;
            opacity: 0.88;
        }

        /* Search Section */
        .search-section {
            background: #fff;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 28px 0;
        }

        .search-form {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-form input[type="text"],
        .search-form select {
            flex: 1;
            min-width: 160px;
            padding: 11px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.92rem;
            color: #334155;
            background: #f8fafc;
            transition: border-color 0.2s;
            outline: none;
        }

        .search-form input[type="text"]:focus,
        .search-form select:focus {
            border-color: #2563eb;
            background: #fff;
        }

        .search-form button {
            padding: 11px 28px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            white-space: nowrap;
        }
        .search-form button:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .btn-reset {
            padding: 11px 18px;
            background: #f1f5f9;
            color: #64748b;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
            white-space: nowrap;
        }
        .btn-reset:hover { background: #e2e8f0; }

        /* Hasil info */
        .result-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .result-info .section-title {
            margin-bottom: 0;
        }

        .result-count {
            font-size: 0.88rem;
            color: #64748b;
            background: #f1f5f9;
            padding: 5px 14px;
            border-radius: 20px;
        }

        .active-filters {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .filter-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
        }

        /* Campaign Grid */
        .campaign-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 24px;
            margin-bottom: 48px;
        }

        .campaign-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
        }
        .campaign-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 28px rgba(0,0,0,0.13);
        }

        .campaign-card img {
            width: 100%;
            height: 195px;
            object-fit: cover;
            display: block;
        }

        .card-content {
            padding: 18px 20px 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .category {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            margin-bottom: 10px;
            letter-spacing: 0.2px;
        }

        .card-content h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
            line-height: 1.35;
        }

        .organizer {
            font-size: 0.82rem;
            color: #94a3b8;
            margin-bottom: 14px;
        }

        /* Mini progress bar di card */
        .card-progress-track {
            background: #e2e8f0;
            border-radius: 999px;
            height: 6px;
            overflow: hidden;
            margin-bottom: 6px;
        }
        .card-progress-fill {
            height: 100%;
            border-radius: 999px;
            background: #2563eb;
        }

        .card-dana {
            display: flex;
            justify-content: space-between;
            font-size: 0.82rem;
            color: #64748b;
            margin-bottom: 10px;
        }
        .card-dana strong {
            color: #2563eb;
        }

        .deadline {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .btn-detail {
            display: block;
            text-align: center;
            background: #2563eb;
            color: #fff !important;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 10px 0;
            border-radius: 8px;
            text-decoration: none;
            margin-top: auto;
            transition: background 0.2s;
        }
        .btn-detail:hover { background: #1d4ed8; }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 64px 20px;
            color: #94a3b8;
        }
        .empty-state .empty-icon { font-size: 3.5rem; margin-bottom: 16px; }
        .empty-state h3 { font-size: 1.2rem; color: #64748b; margin-bottom: 8px; }
        .empty-state p { font-size: 0.92rem; }

        /* Section title */
        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
        }

        @media (max-width: 640px) {
            .search-form { flex-direction: column; }
            .search-form input, .search-form select, .search-form button, .btn-reset {
                width: 100%;
                min-width: unset;
            }
            .hero h2 { font-size: 1.5rem; }
        }
    </style>
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

    <!-- ===== HERO ===== -->
    <section class="hero">
        <div class="container">
            <h2>🤝 Bersama Kita Bisa Membantu</h2>
            <p>Temukan kampanye donasi terpercaya dan jadilah bagian dari perubahan nyata.</p>
        </div>
    </section>

    <!-- ===== SEARCH / FILTER ===== -->
    <section class="search-section">
        <div class="container">
            <form class="search-form" method="GET" action="index.php">
                <input
                    type="text"
                    name="keyword"
                    placeholder="🔍 Judul Kampanye..."
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
                    placeholder="📍 Lokasi..."
                    value="<?= htmlspecialchars($lokasi) ?>"
                >
                <button type="submit">Cari</button>
                <?php if ($ada_filter): ?>
                    <a href="index.php" class="btn-reset">✕ Reset</a>
                <?php endif; ?>
            </form>
        </div>
    </section>

    <!-- ===== LIST KAMPANYE ===== -->
    <main class="container" style="padding-top: 36px;">

        <div class="result-info">
            <h2 class="section-title">
                <?= $ada_filter ? '🔍 Hasil Pencarian' : '🔥 Kampanye Berlangsung' ?>
            </h2>
            <span class="result-count"><?= $jumlah ?> kampanye ditemukan</span>
        </div>

        <!-- Tag filter aktif -->
        <?php if ($ada_filter): ?>
        <div class="active-filters">
            <?php if ($keyword !== ''): ?>
                <span class="filter-tag">🔍 "<?= htmlspecialchars($keyword) ?>"</span>
            <?php endif; ?>
            <?php if ($kategori !== ''): ?>
                <span class="filter-tag">📂 <?= htmlspecialchars($kategori) ?></span>
            <?php endif; ?>
            <?php if ($lokasi !== ''): ?>
                <span class="filter-tag">📍 <?= htmlspecialchars($lokasi) ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Grid Kampanye -->
        <?php if ($jumlah > 0): ?>
        <div class="campaign-grid">
            <?php foreach ($kampanye_list as $k):
                $target    = (float)$k['target_dana'];
                $terkumpul = (float)$k['dana_terkumpul'];
                $persen    = ($target > 0) ? min(100, round(($terkumpul / $target) * 100)) : 0;
                // Fallback gambar jika file tidak ditemukan
                $gambar    = htmlspecialchars($k['gambar']);
            ?>
            <div class="campaign-card">
                <img
                    src="images/<?= $gambar ?>"
                    alt="<?= htmlspecialchars($k['judul']) ?>"
                    onerror="this.src='images/Gempa Manado.jpeg'"
                >
                <div class="card-content">
                    <span class="category"><?= htmlspecialchars($k['kategori']) ?></span>
                    <h3><?= htmlspecialchars($k['judul']) ?></h3>
                    <p class="organizer">Oleh: <?= htmlspecialchars($k['nama_penyelenggara']) ?></p>

                    <!-- Mini progress bar -->
                    <div class="card-progress-track">
                        <div class="card-progress-fill" style="width: <?= $persen ?>%;"></div>
                    </div>
                    <div class="card-dana">
                        <span>Terkumpul: <strong><?= formatRupiah($terkumpul) ?></strong></span>
                        <span><?= $persen ?>%</span>
                    </div>

                    <p>Target: <strong><?= formatRupiah($target) ?></strong></p>
                    <p class="deadline">⏰ Batas Waktu: <?= formatTanggal($k['batas_waktu']) ?></p>

                    <a href="detail.php?id=<?= $k['id'] ?>" class="btn-detail">Lihat Detail →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <!-- Empty state -->
        <div class="empty-state">
            <div class="empty-icon">🔍</div>
            <h3>Kampanye tidak ditemukan</h3>
            <p>Coba gunakan kata kunci lain atau ubah filter pencarian.</p>
        </div>
        <?php endif; ?>

    </main>

    <!-- ===== FOOTER ===== -->
    <footer>
        <div class="container">
            <p>&copy; 2026 BantuSesama - Sistem Crowdfunding Sosial</p>
        </div>
    </footer>

</body>
</html>
