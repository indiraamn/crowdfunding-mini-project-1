<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Sistem Crowdfunding Sosial</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- ===== HEADER ===== -->
    <header>
        <div class="container">
            <h1 class="logo">Bantu<span>Sesama</span></h1>
            <nav>
                <ul>
                    <li><a href="index.html">Beranda</a></li>
                    <li><a href="login.html">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- ===== SEARCH / FILTER ===== -->
    <section class="search-section">
        <div class="container">
            <h2>Temukan Kampanye Donasi</h2>
            <form class="search-form">
                <input type="text" placeholder="Judul Kampanye">

                <select>
                    <option value="">Kategori</option>
                    <option>Bantuan Bencana Alam</option>
                    <option>Pendidikan</option>
                    <option>Kesehatan Lingkungan</option>
                    <option>Pembangunan Fasilitas Umum</option>
                </select>

                <input type="text" placeholder="Lokasi">

                <input type="number" placeholder="Target Dana" step="50000">

                <button type="button">Cari</button>
            </form>
        </div>
    </section>

    <!-- ===== LIST KAMPANYE ===== -->
    <main class="container">
        <h2 class="section-title">Kampanye Berlangsung Saat Ini</h2>

        <div class="campaign-grid">

            <!-- Campaign 1 -->
            <div class="campaign-card">
                <img src="images/Pendidikan.jpg" alt="Poster Kampanye">

                <div class="card-content">
                    <span class="category">Pendidikan</span>

                    <h3>Beasiswa Anak Pesisir</h3>
                    <p class="organizer">Oleh: Yayasan Pendidikan Bangsa</p>

                    <p>Target: <strong>Rp 50.000.000</strong></p>
                    <p>Terkumpul: <strong>Rp 12.500.000</strong></p>
                    <p class="deadline">Batas Waktu: 30 April 2026</p>

                    <a href="detail.html" class="btn-detail">Lihat Detail</a>
                </div>
            </div>

            <!-- Campaign 2 -->
            <div class="campaign-card">
                <img src="images/Gempa Manado.jpeg" alt="Poster Kampanye">

                <div class="card-content">
                    <span class="category">BantuanBencana Alam</span>

                    <h3>Bantuan Korban Gempa</h3>
                    <p class="organizer">Oleh: Relawan Nusantara</p>

                    <p>Target: <strong>Rp 100.000.000</strong></p>
                    <p>Terkumpul: <strong>Rp 45.000.000</strong></p>
                    <p class="deadline">Batas Waktu: 10 Mei 2026</p>

                    <a href="detail.html" class="btn-detail">Lihat Detail</a>
                </div>
            </div>

            <!-- Campaign 3 -->
            <div class="campaign-card">
                <img src="images/1000 pohon.jpg" alt="Poster Kampanye">

                <div class="card-content">
                    <span class="category">Kesehatan Lingkungan</span>

                    <h3>Gerakan Tanam 1000 Pohon</h3>
                    <p class="organizer">Oleh: Komunitas Hijau</p>

                    <p>Target: <strong>Rp 25.000.000</strong></p>
                    <p>Terkumpul: <strong>Rp 10.000.000</strong></p>
                    <p class="deadline">Batas Waktu: 20 Mei 2026</p>

                    <a href="detail.html" class="btn-detail">Lihat Detail</a>
                </div>
            </div>

            <!-- Campaign 4 -->
            <div class="campaign-card">
                <img src="images/jembatan.jpg" alt="Poster Kampanye">

                <div class="card-content">
                    <span class="category">Pembangunan Fasilitas Umum</span>

                    <h3>Pembangunan Jembatan</h3>
                    <p class="organizer">Oleh: Indonesia Bisa</p>

                    <p>Target: <strong>Rp 25.000.000</strong></p>
                    <p>Terkumpul: <strong>Rp 10.000.000</strong></p>
                    <p class="deadline">Batas Waktu: 20 Mei 2026</p>

                    <a href="detail.html" class="btn-detail">Lihat Detail</a>
                </div>
            </div>

        </div>
    </main>

    <!-- ===== FOOTER ===== -->
    <footer>
        <div class="container">
            <p>&copy; 2026 BantuSesama</p>
        </div>
    </footer>

</body>
</html>