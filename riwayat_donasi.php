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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Donasi - BantuSesama</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div class="container">
        <h1 class="logo">Bantu<span>Sesama</span></h1>
        <nav>
            <ul>
                <li><a href="index.php">Beranda</a></li>
                <li><a href="riwayat_donasi.php">Riwayat Donasi</a></li>
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

    <?php if (count($riwayat) > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0" width="100%">
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
                                <a href="<?= htmlspecialchars($r['bukti_transfer']) ?>" target="_blank">Lihat Bukti</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= strtoupper(htmlspecialchars($r['status'])) ?>
                        </td>
                        <td><?= formatTanggal($r['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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