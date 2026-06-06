<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION["donatur_id"])) {
    header("Location: login.php");
    exit;
}

$donatur_id = $_SESSION["donatur_id"];

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function formatTanggal($tanggal) {
    return date('d-m-Y H:i', strtotime($tanggal));
}

$stmt = $conn->prepare("
    SELECT 
        d.id,
        d.nominal,
        d.metode_pembayaran,
        d.pesan_dukungan,
        d.bukti_transfer,
        d.status,
        d.created_at,
        k.judul AS judul_kampanye
    FROM donasi d
    JOIN kampanye k ON d.kampanye_id = k.id
    WHERE d.donatur_id = ?
    ORDER BY d.created_at DESC
");

$stmt->bind_param("i", $donatur_id);
$stmt->execute();
$result = $stmt->get_result();

$riwayat = [];
while ($row = $result->fetch_assoc()) {
    $riwayat[] = $row;
}

// Ringkasan per status
$ringkasan = ['verified' => ['total' => 0, 'count' => 0], 'pending' => ['total' => 0, 'count' => 0], 'rejected' => ['total' => 0, 'count' => 0]];
foreach ($riwayat as $r) {
    $s = $r['status'];
    if (isset($ringkasan[$s])) {
        $ringkasan[$s]['total'] += $r['nominal'];
        $ringkasan[$s]['count']++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Donasi - BantuSesama</title>
    <link rel="stylesheet" href="css/style.css?v=2">
</head>
<body class="history-page">

<header>
    <div class="container">
        <h1 class="logo">Bantu<span>Sesama</span></h1>
        <nav>
            <ul>
                <li><a href="index.php">Beranda</a></li>
                <li><a href="riwayat_donasi.php" class="active">Riwayat Donasi</a></li>
                <li>
                    <span>Halo, <?php echo htmlspecialchars($_SESSION["donatur_nama"]); ?></span>
                </li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="container">
    <h2 class="section-title">Riwayat Donasi Saya</h2>

    <!-- RINGKASAN -->
    <div class="ringkasan-donasi">
        <div class="ringkasan-item ri-verified">
            ✅ Verified: <?= formatRupiah($ringkasan['verified']['total']) ?> (<?= $ringkasan['verified']['count'] ?> donasi)
        </div>
        <div class="ringkasan-item ri-pending">
            ⏳ Pending: <?= formatRupiah($ringkasan['pending']['total']) ?> (<?= $ringkasan['pending']['count'] ?> donasi)
        </div>
        <div class="ringkasan-item ri-rejected">
            ❌ Ditolak: <?= formatRupiah($ringkasan['rejected']['total']) ?> (<?= $ringkasan['rejected']['count'] ?> donasi)
        </div>
    </div>

    <?php if (count($riwayat) > 0): ?>
        <div class="table-wrapper">
            <table class="history-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kampanye</th>
                    <th>Nominal</th>
                    <th>Metode</th>
                    <th>Pesan</th>
                    <th>Bukti</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($riwayat as $r): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($r['judul_kampanye']) ?></td>
                        <td><?= formatRupiah($r['nominal']) ?></td>
                        <td><?= htmlspecialchars($r['metode_pembayaran']) ?></td>
                        <td>
                            <?= $r['pesan_dukungan'] ? htmlspecialchars($r['pesan_dukungan']) : '-' ?>
                        </td>
                        <td>
                            <?php if (!empty($r['bukti_transfer'])): ?>
                                <a href="<?= htmlspecialchars($r['bukti_transfer']) ?>" target="_blank" class="btn-proof"> 
                                    Lihat Bukti
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                        <span class="status-badge status-<?= strtolower(htmlspecialchars($r['status'])) ?>">
                            <?= strtoupper(htmlspecialchars($r['status'])) ?>
                        </span>
                        </td>
                        <td><?= formatTanggal($r['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align:center; padding:40px 0; color:#888;">
            Kamu belum pernah melakukan donasi.
        </p>
    <?php endif; ?>
</main>

<footer>
    <div class="container">
        <p>&copy; 2026 BantuSesama</p>
    </div>
</footer>

</body>
</html>