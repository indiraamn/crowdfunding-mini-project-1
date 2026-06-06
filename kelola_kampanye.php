<?php
session_start();
require 'koneksi.php';

// cek login penyelenggara
if (!isset($_SESSION['penyelenggara_id'])) {
    header("Location: login.php");
    exit;
}

$penyelenggara_id = $_SESSION['penyelenggara_id'];
$penyelenggara_nama = $_SESSION['penyelenggara_nama'];

// helper function
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
function formatTanggal($tanggal) {
    $bulan = [
        '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
        '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
        '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
    ];
    list($y, $m, $d) = explode('-', substr($tanggal, 0, 10));
    return "$d {$bulan[$m]} $y";
}

$pesan_sukses = "";
$pesan_error  = "";

// untuk tambah kampanye
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah') {
    $judul       = trim($_POST['judul'] ?? '');
    $kategori    = trim($_POST['kategori'] ?? '');
    $lokasi      = trim($_POST['lokasi'] ?? '');
    $deskripsi   = trim($_POST['deskripsi'] ?? '');
    $target_dana = (float)($_POST['target_dana'] ?? 0);
    $batas_waktu = $_POST['batas_waktu'] ?? '';
    $rekening    = trim($_POST['rekening_info'] ?? '');
    $gambar_nama = '';

    // Upload gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $ext_allowed = ['jpg','jpeg','png','gif','webp'];
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $ext_allowed)) {
            $folder = 'images/';
            if (!is_dir($folder)) mkdir($folder, 0777, true);
            $gambar_nama = 'kampanye_' . time() . '_' . $penyelenggara_id . '.' . $ext;
            move_uploaded_file($_FILES['gambar']['tmp_name'], $folder . $gambar_nama);
        } else {
            $pesan_error = "Format gambar tidak valid (harus JPG/PNG/GIF/WEBP).";
        }
    }

    if ($pesan_error === '' && $judul && $kategori && $lokasi && $target_dana > 0 && $batas_waktu) {
        $stmt = $conn->prepare("
            INSERT INTO kampanye (penyelenggara_id, judul, kategori, lokasi, deskripsi, target_dana, dana_terkumpul, batas_waktu, rekening_info, gambar, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->bind_param("issssdsss", $penyelenggara_id, $judul, $kategori, $lokasi, $deskripsi, $target_dana, $batas_waktu, $rekening, $gambar_nama);
        if ($stmt->execute()) {
            $pesan_sukses = "Kampanye berhasil ditambahkan.";
        } else {
            $pesan_error = "Gagal menyimpan kampanye ke database.";
        }
        $stmt->close();
    } elseif ($pesan_error === '') {
        $pesan_error = "Semua kolom wajib diisi.";
    }
}

// untuk edit kampanye
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $kampanye_id = (int)$_POST['kampanye_id'];
    $judul       = trim($_POST['judul'] ?? '');
    $kategori    = trim($_POST['kategori'] ?? '');
    $lokasi      = trim($_POST['lokasi'] ?? '');
    $deskripsi   = trim($_POST['deskripsi'] ?? '');
    $target_dana = (float)($_POST['target_dana'] ?? 0);
    $batas_waktu = $_POST['batas_waktu'] ?? '';
    $rekening    = trim($_POST['rekening_info'] ?? '');

    // Pastikan kampanye milik penyelenggara ini
    $cek = $conn->prepare("SELECT id, gambar FROM kampanye WHERE id = ? AND penyelenggara_id = ?");
    $cek->bind_param("ii", $kampanye_id, $penyelenggara_id);
    $cek->execute();
    $res_cek = $cek->get_result();
    $data_lama = $res_cek->fetch_assoc();
    $cek->close();

    if (!$data_lama) {
        $pesan_error = "Kampanye tidak ditemukan atau bukan milik Anda.";
    } else {
        $gambar_nama = $data_lama['gambar'];

        // Jika ada upload gambar baru
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $ext_allowed = ['jpg','jpeg','png','gif','webp'];
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $ext_allowed)) {
                $folder = 'images/';
                if (!is_dir($folder)) mkdir($folder, 0777, true);
                $gambar_nama = 'kampanye_' . time() . '_' . $penyelenggara_id . '.' . $ext;
                move_uploaded_file($_FILES['gambar']['tmp_name'], $folder . $gambar_nama);
            } else {
                $pesan_error = "Format gambar tidak valid.";
            }
        }

        if ($pesan_error === '') {
            $upd = $conn->prepare("
                UPDATE kampanye SET judul=?, kategori=?, lokasi=?, deskripsi=?, target_dana=?, batas_waktu=?, rekening_info=?, gambar=?, updated_at=NOW()
                WHERE id = ? AND penyelenggara_id = ?
            ");
            $upd->bind_param("ssssdsssii", $judul, $kategori, $lokasi, $deskripsi, $target_dana, $batas_waktu, $rekening, $gambar_nama, $kampanye_id, $penyelenggara_id);
            if ($upd->execute()) {
                $pesan_sukses = "Kampanye berhasil diperbarui.";
            } else {
                $pesan_error = "Gagal memperbarui kampanye.";
            }
            $upd->close();
        }
    }
}

// untuk hapus kampanye
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'hapus') {
    $kampanye_id = (int)$_POST['kampanye_id'];

    // Cek dana terkumpul
    $cek = $conn->prepare("SELECT dana_terkumpul FROM kampanye WHERE id = ? AND penyelenggara_id = ?");
    $cek->bind_param("ii", $kampanye_id, $penyelenggara_id);
    $cek->execute();
    $res_cek = $cek->get_result()->fetch_assoc();
    $cek->close();

    if (!$res_cek) {
        $pesan_error = "Kampanye tidak ditemukan.";
    } elseif ((float)$res_cek['dana_terkumpul'] >= 10000) {
        $pesan_error = "Kampanye tidak dapat dihapus karena sudah memiliki dana terkumpul (≥ Rp10.000).";
    } else {
        $del = $conn->prepare("DELETE FROM kampanye WHERE id = ? AND penyelenggara_id = ?");
        $del->bind_param("ii", $kampanye_id, $penyelenggara_id);
        if ($del->execute()) {
            $pesan_sukses = "Kampanye berhasil dihapus.";
        } else {
            $pesan_error = "Gagal menghapus kampanye.";
        }
        $del->close();
    }
}

// verifikasi donasi : terima/tolak
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['terima','tolak'])) {
    $donasi_id   = (int)$_POST['donasi_id'];
    $aksi        = $_POST['action'];

    // Pastikan donasi ini milik kampanye yang dikelola penyelenggara ini
    $cek = $conn->prepare("
        SELECT d.id, d.nominal, d.kampanye_id
        FROM donasi d
        JOIN kampanye k ON d.kampanye_id = k.id
        WHERE d.id = ? AND k.penyelenggara_id = ? AND d.status = 'pending'
    ");
    $cek->bind_param("ii", $donasi_id, $penyelenggara_id);
    $cek->execute();
    $donasi_data = $cek->get_result()->fetch_assoc();
    $cek->close();

    if (!$donasi_data) {
        $pesan_error = "Donasi tidak ditemukan atau sudah diverifikasi.";
    } else {
        if ($aksi === 'terima') {
            // Update status donasi ke verified + tambah dana terkumpul
            $nominal     = (float)$donasi_data['nominal'];
            $kampanye_id = (int)$donasi_data['kampanye_id'];

            $upd_donasi = $conn->prepare("UPDATE donasi SET status='verified', updated_at=NOW() WHERE id=?");
            $upd_donasi->bind_param("i", $donasi_id);
            $upd_donasi->execute();
            $upd_donasi->close();

            $upd_dana = $conn->prepare("UPDATE kampanye SET dana_terkumpul = dana_terkumpul + ?, updated_at=NOW() WHERE id=?");
            $upd_dana->bind_param("di", $nominal, $kampanye_id);
            $upd_dana->execute();
            $upd_dana->close();

            $pesan_sukses = "Donasi berhasil diverifikasi dan dana terkumpul telah diperbarui.";
        } else {
            // Tolak: update status saja, tidak tambah dana
            $upd_donasi = $conn->prepare("UPDATE donasi SET status='rejected', updated_at=NOW() WHERE id=?");
            $upd_donasi->bind_param("i", $donasi_id);
            $upd_donasi->execute();
            $upd_donasi->close();

            $pesan_sukses = "Donasi berhasil ditolak.";
        }
    }
}

// ambil data kampanye untuk penyelenggara ini
$stmt_k = $conn->prepare("
    SELECT * FROM kampanye WHERE penyelenggara_id = ? ORDER BY created_at DESC
");
$stmt_k->bind_param("i", $penyelenggara_id);
$stmt_k->execute();
$result_k = $stmt_k->get_result();
$kampanye_list = [];
while ($row = $result_k->fetch_assoc()) {
    $kampanye_list[] = $row;
}
$stmt_k->close();

// ambil data donasi untuk kampanye-kampanye penyelenggara ini
$stmt_d = $conn->prepare("
    SELECT 
        d.id AS donasi_id,
        d.kampanye_id,
        d.nominal,
        d.metode_pembayaran,
        d.pesan_dukungan,
        d.bukti_transfer,
        d.status,
        d.created_at,
        k.judul AS judul_kampanye,
        dt.nama AS nama_donatur,
        dt.email AS email_donatur
    FROM donasi d
    JOIN kampanye k ON d.kampanye_id = k.id
    JOIN donatur dt ON d.donatur_id = dt.id
    WHERE k.penyelenggara_id = ?
    ORDER BY d.created_at DESC
");
$stmt_d->bind_param("i", $penyelenggara_id);
$stmt_d->execute();
$result_d = $stmt_d->get_result();
$donasi_list = [];
while ($row = $result_d->fetch_assoc()) {
    $donasi_list[] = $row;
}
$stmt_d->close();

// Ringkasan per kampanye
$ringkasan = [];
foreach ($donasi_list as $d) {
    $kid = $d['kampanye_id'];
    if (!isset($ringkasan[$kid])) {
        $ringkasan[$kid] = ['verified'=>0,'pending'=>0,'rejected'=>0];
    }
    $ringkasan[$kid][$d['status']] += (float)$d['nominal'];
}

// Data kampanye yang sedang di-edit (jika ada ?edit=id)
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt_e = $conn->prepare("SELECT * FROM kampanye WHERE id = ? AND penyelenggara_id = ?");
    $stmt_e->bind_param("ii", $edit_id, $penyelenggara_id);
    $stmt_e->execute();
    $edit_data = $stmt_e->get_result()->fetch_assoc();
    $stmt_e->close();
}

// Tab aktif
$tab = $_GET['tab'] ?? 'kampanye';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kampanye - BantuSesama</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* ===== TAB NAV ===== */
        .tab-nav {
            display: flex;
            gap: 0;
            border-bottom: 2px solid #1976D2;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }
        .tab-nav a {
            padding: 10px 24px;
            background: #f0f4ff;
            color: #1976D2;
            text-decoration: none;
            font-weight: 600;
            border: 1px solid #c5d8f6;
            border-bottom: none;
            border-radius: 6px 6px 0 0;
            transition: background 0.2s;
        }
        .tab-nav a.active, .tab-nav a:hover {
            background: #1976D2;
            color: #fff;
        }

        /* INI BAGIAN CSS - BERDIRI SENDIRI BIAR GA KEBANYAKAN BARIS HUHU */
        /* pesan */
        .msg-sukses { background:#e6f9ee; border-left:4px solid #27ae60; color:#1a7a44; padding:12px 16px; border-radius:6px; margin-bottom:18px; }
        .msg-error  { background:#fdecea; border-left:4px solid #e53935; color:#b71c1c; padding:12px 16px; border-radius:6px; margin-bottom:18px; }

        /* tabel */
        .tabel-kelola { width:100%; border-collapse:collapse; margin-bottom:28px; font-size:0.93rem; }
        .tabel-kelola th { background:#1976D2; color:#fff; padding:10px 12px; text-align:left; }
        .tabel-kelola td { padding:10px 12px; border-bottom:1px solid #e0e7ef; vertical-align:middle; }
        .tabel-kelola tr:hover td { background:#f5f8ff; }

        /* status badge */
        .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:0.8rem; font-weight:700; text-transform:uppercase; }
        .badge-verified  { background:#d4f8e5; color:#1a7a44; }
        .badge-pending   { background:#fff3cd; color:#856404; }
        .badge-rejected  { background:#fde8e8; color:#b71c1c; }

        /* tombol */
        .btn { display:inline-block; padding:6px 14px; border-radius:5px; border:none; cursor:pointer; font-size:0.85rem; font-weight:600; text-decoration:none; transition:opacity .2s; }
        .btn:hover { opacity:0.85; }
        .btn-edit     { background:#f39c12; color:#fff; }
        .btn-hapus    { background:#e53935; color:#fff; }
        .btn-terima   { background:#27ae60; color:#fff; }
        .btn-tolak    { background:#e53935; color:#fff; }
        .btn-primary  { background:#1976D2; color:#fff; padding:10px 24px; font-size:1rem; }
        .btn-secondary{ background:#888; color:#fff; }
        .btn-sm       { padding:4px 10px; font-size:0.8rem; }

        /* form kampanye */
        .form-kampanye { background:#f8faff; border:1px solid #d0e4f7; border-radius:10px; padding:28px; margin-bottom:32px; }
        .form-kampanye h3 { margin-top:0; color:#1976D2; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .form-group-k { display:flex; flex-direction:column; gap:5px; margin-bottom:14px; }
        .form-group-k label { font-weight:600; font-size:0.9rem; color:#333; }
        .form-group-k input,
        .form-group-k select,
        .form-group-k textarea { border:1px solid #c5d8f6; border-radius:5px; padding:8px 10px; font-size:0.95rem; width:100%; box-sizing:border-box; }
        .form-group-k textarea { resize:vertical; min-height:90px; }
        .form-actions { display:flex; gap:12px; margin-top:10px; }

        /* ringkasan dana */
        .ringkasan-box { background:#fff; border:1px solid #d0e4f7; border-radius:8px; padding:14px 18px; margin-bottom:12px; }
        .ringkasan-box h4 { margin:0 0 8px; color:#1976D2; font-size:1rem; }
        .ringkasan-row { display:flex; gap:18px; flex-wrap:wrap; }
        .ringkasan-item { font-size:0.88rem; font-weight:600; }
        .ri-verified { color:#1a7a44; }
        .ri-pending  { color:#856404; }
        .ri-rejected { color:#b71c1c; }

        /* responsive */
        @media (max-width:640px) {
            .form-row { grid-template-columns:1fr; }
            .tabel-kelola { font-size:0.8rem; }
            .tabel-kelola th, .tabel-kelola td { padding:7px 6px; }
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
                <li><a href="kelola_kampanye.php" class="active">Dashboard</a></li>
                <li><span>Halo, <?= htmlspecialchars($penyelenggara_nama) ?></span></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="container" style="padding-top:28px">

    <h2 style="color:#1976D2; margin-bottom:6px">Dashboard Penyelenggara</h2>
    <p style="color:#555; margin-bottom:22px">Kelola kampanye dan verifikasi donasi Anda di sini.</p>

    <?php if ($pesan_sukses): ?>
        <div class="msg-sukses"><?= htmlspecialchars($pesan_sukses) ?></div>
    <?php endif; ?>
    <?php if ($pesan_error): ?>
        <div class="msg-error"><?= htmlspecialchars($pesan_error) ?></div>
    <?php endif; ?>

    <!-- TAB NAV -->
    <div class="tab-nav">
        <a href="?tab=kampanye" class="<?= $tab === 'kampanye' ? 'active' : '' ?>">📋 Kampanye Saya</a>
        <a href="?tab=tambah"   class="<?= $tab === 'tambah'   ? 'active' : '' ?>">➕ Tambah Kampanye</a>
        <a href="?tab=donasi"   class="<?= $tab === 'donasi'   ? 'active' : '' ?>">💰 Verifikasi Donasi</a>
    </div>

    <!-- TAB : KAMPANYE SAYA -->
    <?php if ($tab === 'kampanye'): ?>

        <!-- FORM EDIT (jika ada ?edit=id) -->
        <?php if ($edit_data): ?>
        <div class="form-kampanye">
            <h3>✏️ Edit Kampanye: <?= htmlspecialchars($edit_data['judul']) ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="kampanye_id" value="<?= $edit_data['id'] ?>">

                <div class="form-row">
                    <div class="form-group-k">
                        <label>Judul Kampanye *</label>
                        <input type="text" name="judul" required value="<?= htmlspecialchars($edit_data['judul']) ?>">
                    </div>
                    <div class="form-group-k">
                        <label>Kategori *</label>
                        <select name="kategori" required>
                            <?php foreach (['Bencana','Pendidikan','Kesehatan','Lingkungan','Fasilitas Umum','Pemberdayaan','Ekonomi','Lainnya'] as $kat): ?>
                                <option value="<?= $kat ?>" <?= $edit_data['kategori']===$kat?'selected':'' ?>><?= $kat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group-k">
                        <label>Lokasi *</label>
                        <input type="text" name="lokasi" required value="<?= htmlspecialchars($edit_data['lokasi']) ?>">
                    </div>
                    <div class="form-group-k">
                        <label>Target Dana (Rp) *</label>
                        <input type="number" name="target_dana" required min="10000" step="1000" value="<?= $edit_data['target_dana'] ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group-k">
                        <label>Batas Waktu *</label>
                        <input type="date" name="batas_waktu" required value="<?= $edit_data['batas_waktu'] ?>">
                    </div>
                    <div class="form-group-k">
                        <label>Ganti Gambar/Poster</label>
                        <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.gif,.webp">
                        <?php if ($edit_data['gambar']): ?>
                            <small>Gambar saat ini: <?= htmlspecialchars($edit_data['gambar']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group-k">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi"><?= htmlspecialchars($edit_data['deskripsi']) ?></textarea>
                </div>
                <div class="form-group-k">
                    <label>Info Rekening / Metode Donasi</label>
                    <textarea name="rekening_info"><?= htmlspecialchars($edit_data['rekening_info']) ?></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                    <a href="?tab=kampanye" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- DAFTAR KAMPANYE -->
        <?php if (count($kampanye_list) === 0): ?>
            <p style="text-align:center;color:#888;padding:40px 0">Anda belum memiliki kampanye. Silakan tambah kampanye baru.</p>
        <?php else: ?>
        <table class="tabel-kelola">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Target</th>
                    <th>Terkumpul</th>
                    <th>Batas Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($kampanye_list as $k): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <strong><?= htmlspecialchars($k['judul']) ?></strong><br>
                        <small style="color:#666"><?= htmlspecialchars($k['lokasi']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($k['kategori']) ?></td>
                    <td><?= formatRupiah($k['target_dana']) ?></td>
                    <td><?= formatRupiah($k['dana_terkumpul']) ?></td>
                    <td><?= formatTanggal($k['batas_waktu']) ?></td>
                    <td style="white-space:nowrap">
                        <a href="?tab=kampanye&edit=<?= $k['id'] ?>" class="btn btn-edit btn-sm">✏️ Edit</a>
                        &nbsp;
                        <?php if ((float)$k['dana_terkumpul'] >= 10000): ?>
                            <span class="btn btn-hapus btn-sm" style="opacity:0.5;cursor:not-allowed" title="Tidak bisa dihapus, sudah ada dana terkumpul">🗑 Hapus</span>
                        <?php else: ?>
                            <form method="POST" style="display:inline" onsubmit="return confirm('Yakin hapus kampanye ini?')">
                                <input type="hidden" name="action" value="hapus">
                                <input type="hidden" name="kampanye_id" value="<?= $k['id'] ?>">
                                <button type="submit" class="btn btn-hapus btn-sm">🗑 Hapus</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

    <!-- TAB : TAMBAH KAMPANYE -->
    <?php elseif ($tab === 'tambah'): ?>

        <div class="form-kampanye">
            <h3>➕ Tambah Kampanye Baru</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="tambah">

                <div class="form-row">
                    <div class="form-group-k">
                        <label>Judul Kampanye *</label>
                        <input type="text" name="judul" required placeholder="Contoh: Bantu Korban Banjir Manado">
                    </div>
                    <div class="form-group-k">
                        <label>Kategori *</label>
                        <select name="kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach (['Bencana','Pendidikan','Kesehatan','Lingkungan','Fasilitas Umum','Pemberdayaan','Ekonomi','Lainnya'] as $kat): ?>
                                <option value="<?= $kat ?>"><?= $kat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group-k">
                        <label>Lokasi *</label>
                        <input type="text" name="lokasi" required placeholder="Kota/Provinsi">
                    </div>
                    <div class="form-group-k">
                        <label>Target Dana (Rp) *</label>
                        <input type="number" name="target_dana" required min="10000" step="1000" placeholder="50000000">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group-k">
                        <label>Batas Waktu *</label>
                        <input type="date" name="batas_waktu" required min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group-k">
                        <label>Gambar/Poster Kampanye</label>
                        <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.gif,.webp">
                    </div>
                </div>
                <div class="form-group-k">
                    <label>Deskripsi Kampanye</label>
                    <textarea name="deskripsi" placeholder="Ceritakan latar belakang dan tujuan kampanye ini..."></textarea>
                </div>
                <div class="form-group-k">
                    <label>Info Rekening / Metode Donasi</label>
                    <textarea name="rekening_info" placeholder="Contoh: BCA 1234567890 a.n. Yayasan Peduli"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Simpan Kampanye</button>
                    <a href="?tab=kampanye" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>

    <!-- TAB : VERIFIKASI DONASI -->
    <?php elseif ($tab === 'donasi'): ?>

        <!-- RINGKASAN DANA PER KAMPANYE -->
        <?php if (!empty($ringkasan)): ?>
        <h3 style="color:#1976D2;margin-bottom:12px">📊 Ringkasan Dana per Kampanye</h3>
        <?php foreach ($kampanye_list as $k): 
            $kid = $k['id'];
            $rv = $ringkasan[$kid]['verified']  ?? 0;
            $rp = $ringkasan[$kid]['pending']   ?? 0;
            $rr = $ringkasan[$kid]['rejected']  ?? 0;
        ?>
        <div class="ringkasan-box">
            <h4><?= htmlspecialchars($k['judul']) ?></h4>
            <div class="ringkasan-row">
                <span class="ringkasan-item ri-verified">✅ Verified: <?= formatRupiah($rv) ?></span>
                <span class="ringkasan-item ri-pending">⏳ Pending: <?= formatRupiah($rp) ?></span>
                <span class="ringkasan-item ri-rejected">❌ Ditolak: <?= formatRupiah($rr) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
        <br>
        <?php endif; ?>

        <!-- TABEL SEMUA DONASI -->
        <h3 style="color:#1976D2;margin-bottom:12px">📋 Semua Donasi</h3>
        <?php if (count($donasi_list) === 0): ?>
            <p style="text-align:center;color:#888;padding:40px 0">Belum ada donasi untuk kampanye Anda.</p>
        <?php else: ?>
        <table class="tabel-kelola">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kampanye</th>
                    <th>Donatur</th>
                    <th>Nominal</th>
                    <th>Metode</th>
                    <th>Bukti</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($donasi_list as $d): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td style="max-width:160px"><small><?= htmlspecialchars($d['judul_kampanye']) ?></small></td>
                    <td>
                        <?= htmlspecialchars($d['nama_donatur']) ?><br>
                        <small style="color:#666"><?= htmlspecialchars($d['email_donatur']) ?></small>
                    </td>
                    <td><strong><?= formatRupiah($d['nominal']) ?></strong></td>
                    <td><?= htmlspecialchars($d['metode_pembayaran']) ?></td>
                    <td>
                        <?php if ($d['bukti_transfer']): ?>
                            <a href="<?= htmlspecialchars($d['bukti_transfer']) ?>" target="_blank" class="btn btn-sm" style="background:#1976D2;color:#fff">Lihat</a>
                        <?php else: ?> - <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($d['status'] === 'verified'): ?>
                            <span class="badge badge-verified">Verified</span>
                        <?php elseif ($d['status'] === 'pending'): ?>
                            <span class="badge badge-pending">Pending</span>
                        <?php else: ?>
                            <span class="badge badge-rejected">Ditolak</span>
                        <?php endif; ?>
                    </td>
                    <td style="white-space:nowrap;font-size:0.8rem"><?= date('d/m/Y H:i', strtotime($d['created_at'])) ?></td>
                    <td style="white-space:nowrap">
                        <?php if ($d['status'] === 'pending'): ?>
                            <!-- Terima -->
                            <form method="POST" style="display:inline" onsubmit="return confirm('Terima donasi ini?')">
                                <input type="hidden" name="action" value="terima">
                                <input type="hidden" name="donasi_id" value="<?= $d['donasi_id'] ?>">
                                <input type="hidden" name="tab_redirect" value="donasi">
                                <button type="submit" class="btn btn-terima btn-sm">✅ Terima</button>
                            </form>
                            &nbsp;
                            <!-- Tolak -->
                            <form method="POST" style="display:inline" onsubmit="return confirm('Tolak donasi ini?')">
                                <input type="hidden" name="action" value="tolak">
                                <input type="hidden" name="donasi_id" value="<?= $d['donasi_id'] ?>">
                                <button type="submit" class="btn btn-tolak btn-sm">❌ Tolak</button>
                            </form>
                        <?php else: ?>
                            <span style="color:#aaa;font-size:0.8rem">Sudah diproses</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

    <?php endif; ?>

</main>

<!-- ===== FOOTER ===== -->
<footer>
    <div class="container">
        <p>&copy; 2026 BantuSesama</p>
    </div>
</footer>

<script>
// Redirect ke tab yang benar setelah aksi POST
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
// Jika aksi verifikasi, tetap di tab donasi
const action = "<?= $_POST['action'] ?? '' ?>";
if (['terima','tolak'].includes(action)) {
    history.replaceState(null, '', '?tab=donasi');
} else if (['tambah'].includes(action)) {
    history.replaceState(null, '', '?tab=tambah');
} else {
    history.replaceState(null, '', '?tab=kampanye');
}
<?php endif; ?>
</script>

</body>
</html>