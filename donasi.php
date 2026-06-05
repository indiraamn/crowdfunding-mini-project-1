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
$error = "";
$success = "";
if (isset($_GET["success"]) && $_GET["success"] == 1) {
    $success = "Donasi berhasil dikirim. Status donasi Anda masih PENDING dan menunggu verifikasi.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nominal = isset($_POST["nominal"]) ? (float)$_POST["nominal"] : 0;
    $metode_pembayaran = $_POST["pembayaran"] ?? "";
    $pesan_dukungan = trim($_POST["pesan"] ?? "");

    if ($nominal < 10000) {
        $error = "Nominal donasi minimal Rp10.000.";
    } elseif ($metode_pembayaran == "") {
        $error = "Metode pembayaran wajib dipilih.";
    } elseif (!isset($_FILES["bukti"]) || $_FILES["bukti"]["error"] != UPLOAD_ERR_OK) {
        $error = "Bukti transfer wajib diupload.";
    } else {
        $nama_asli = $_FILES["bukti"]["name"];
        $tmp_file = $_FILES["bukti"]["tmp_name"];
        $ext = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));

        $allowed_ext = ["jpg", "jpeg", "png", "pdf"];

        if (!in_array($ext, $allowed_ext)) {
            $error = "Bukti transfer hanya boleh JPG, PNG, atau PDF.";
        } else {
            $folder_upload = "bukti/";

            if (!is_dir($folder_upload)) {
                mkdir($folder_upload, 0777, true);
            }

            $nama_file_baru = "bukti_" . time() . "_" . $donatur_id . "." . $ext;
            $path_simpan = $folder_upload . $nama_file_baru;

            if (move_uploaded_file($tmp_file, $path_simpan)) {
                $status = "pending";

                if ($pesan_dukungan == "") {
                    $pesan_dukungan = null;
                }

                $stmt_insert = $conn->prepare("
                    INSERT INTO donasi 
                    (kampanye_id, donatur_id, nominal, metode_pembayaran, pesan_dukungan, bukti_transfer, status, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");

                $stmt_insert->bind_param(
                    "iidssss",
                    $id,
                    $donatur_id,
                    $nominal,
                    $metode_pembayaran,
                    $pesan_dukungan,
                    $path_simpan,
                    $status
                );

                if ($stmt_insert->execute()) {
                    header("Location: donasi.php?id=" . $id . "&success=1");
                    exit;
                } else {
                    $error = "Gagal menyimpan donasi ke database.";
                }
            } else {
                $error = "Gagal mengupload bukti transfer.";
            }
        }
    }
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
            <?php if ($error != ""): ?>
    <p style="color:red; text-align:center;"><?php echo $error; ?></p>
<?php endif; ?>

<?php if ($success != ""): ?>
    <p style="color:green; text-align:center;"><?php echo $success; ?></p>
<?php endif; ?>

            <!-- karna harus 4 halaman, act sukses nya disini aja (semoga bisa) -->
            <form method="POST" enctype="multipart/form-data">
                
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
                    <input type="number" id="nominal" name="nominal" required min="10000" step="5000">
                </div>

                <div class="form-group">
                    <label for="pembayaran">Metode Pembayaran</label>
                    <select id="pembayaran" name="pembayaran" required>
                        <option value="">-- Pilih Metode --</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="E-wallet">E-Wallet</option>
                        <option value="QRIS">QRIS</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="pesan">Pesan Dukungan</label>
                    <textarea id="pesan" name="pesan" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label for="bukti">Bukti Transfer</label>
                    <input type="file" id="bukti" name="bukti" accept=".jpg,.jpeg,.png,.pdf" required>
                </div>

                <button type="submit" class="btn-submit">Kirim Donasi</button>

            </form>

            
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