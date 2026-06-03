-- =============================================
-- DATA INSERT untuk Database: crowdfunding
-- =============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Data untuk tabel `penyelenggara`
-- --------------------------------------------------------

INSERT INTO `penyelenggara` (`id`, `nama_penyelenggara`, `email`, `no_telepon`, `alamat`, `password`, `created_at`) VALUES
(1, 'Yayasan Peduli Nusantara', 'peduli.nusantara@gmail.com', '081234567801', 'Jl. Sudirman No. 12, Jakarta Pusat, DKI Jakarta', '$2y$10$abcdefghijklmnopqrstuuVwXyZ1234567890abcdefghijklmno', '2025-01-05 08:00:00'),
(2, 'Komunitas Bantu Sesama', 'bantusesama.id@gmail.com', '081234567802', 'Jl. Malioboro No. 45, Yogyakarta, DIY', '$2y$10$abcdefghijklmnopqrstuuVwXyZ1234567890abcdefghijklmno', '2025-01-10 09:00:00'),
(3, 'Relawan Harapan Indonesia', 'harapan.indonesia@yahoo.com', '081234567803', 'Jl. Diponegoro No. 7, Surabaya, Jawa Timur', '$2y$10$abcdefghijklmnopqrstuuVwXyZ1234567890abcdefghijklmno', '2025-01-15 10:00:00'),
(4, 'Yayasan Cahaya Bangsa', 'cahaya.bangsa@gmail.com', '081234567804', 'Jl. Gatot Subroto No. 33, Bandung, Jawa Barat', '$2y$10$abcdefghijklmnopqrstuuVwXyZ1234567890abcdefghijklmno', '2025-02-01 08:30:00'),
(5, 'Gerakan Cinta Lingkungan', 'cinta.lingkungan@gmail.com', '081234567805', 'Jl. Ahmad Yani No. 88, Semarang, Jawa Tengah', '$2y$10$abcdefghijklmnopqrstuuVwXyZ1234567890abcdefghijklmno', '2025-02-10 11:00:00');

-- --------------------------------------------------------
-- Data untuk tabel `donatur`
-- --------------------------------------------------------

INSERT INTO `donatur` (`id`, `nama`, `email`, `no_telepon`, `password`, `created_at`) VALUES
(1,  'Budi Santoso',       'budi.santoso@gmail.com',       '08111000001', '$2y$10$abcdefghijklmnopqrstuuVwXyZ1234567890abcdefghijklmno', '2025-01-20 08:00:00'),
(2,  'Siti Rahayu',        'siti.rahayu@gmail.com',        '08111000002', '$2y$10$abcdefghijklmnopqrstuuVwXyZ1234567890abcdefghijklmno', '2025-01-21 09:15:00'),
(3,  'Ahmad Fauzi',        'ahmad.fauzi@yahoo.com',        '08111000003', '$2y$10$abcdefghijklmnopqrstuuVwXyZ1234567890abcdefghijklmno', '2025-01-22 10:30:00'),
(4,  'Dewi Lestari',       'dewi.lestari@gmail.com',       '08111000004', '$2y$10$abcdefghijklmnopqrstuuVwXyZ1234567890abcdefghijklmno', '2025-02-01 07:45:00'),
(5,  'Rizky Pratama',      'rizky.pratama@gmail.com',      '08111000005', '$2y$10$abcdefghijklmnopqrstuuVwXyZ1234567890abcdefghijklmno', '2025-02-03 13:00:00'),
(6,  'Eka Wahyuni',        'eka.wahyuni@outlook.com',      '08111000006', '$2y$10$abcdefghijklmnopqrstuuVwXyZ1234567890abcdefghijklmno', '2025-02-05 15:20:00'),
(7,  'Hendra Kusuma',      'hendra.kusuma@gmail.com',      '08111000007', '$2y$10$abcdefghijklmnopqrstuuVwXyZ1234567890abcdefghijklmno', '2025-02-10 08:00:00'),
(8,  'Nur Aini',           'nur.aini@gmail.com',           '08111000008', '$2y$10$abcdefghijklmnopqrstuuVwXyZ1234567890abcdefghijklmno', '2025-02-15 09:30:00'),
(9,  'Fajar Setiawan',     'fajar.setiawan@gmail.com',     '08111000009', '$2y$10$abcdefghijklmnopqrstuuVwXyZ1234567890abcdefghijklmno', '2025-03-01 11:00:00'),
(10, 'Indah Permatasari',  'indah.permatasari@gmail.com',  '08111000010', '$2y$10$abcdefghijklmnopqrstuuVwXyZ1234567890abcdefghijklmno', '2025-03-05 14:00:00'),
(11, 'Doni Firmansyah',    'doni.firmansyah@yahoo.com',    '08111000011', '$2y$10$abcdefghijklmnopqrstuuVwXyZ1234567890abcdefghijklmno', '2025-03-10 10:15:00'),
(12, 'Yuli Astuti',        'yuli.astuti@gmail.com',        '08111000012', '$2y$10$abcdefghijklmnopqrstuuVwXyZ1234567890abcdefghijklmno', '2025-03-12 08:45:00');

-- --------------------------------------------------------
-- Data untuk tabel `kampanye`
-- --------------------------------------------------------

INSERT INTO `kampanye` (`id`, `penyelenggara_id`, `judul`, `kategori`, `lokasi`, `deskripsi`, `target_dana`, `dana_terkumpul`, `batas_waktu`, `gambar`, `rekening_info`, `status`, `created_at`, `updated_at`) VALUES
(1,  1, 'Bantuan Korban Banjir Kalimantan Selatan',
    'Bencana Alam', 'Banjarmasin, Kalimantan Selatan',
    'Banjir besar melanda Kalimantan Selatan dan menyebabkan ribuan warga kehilangan tempat tinggal. Donasi akan digunakan untuk kebutuhan dasar seperti makanan, air bersih, obat-obatan, dan selimut.',
    50000000.00, 32500000.00, '2025-06-30',
    'banjir_kalsel.jpg',
    'BRI: 1234-5678-9012 a.n. Yayasan Peduli Nusantara',
    'aktif', '2025-02-01 08:00:00', '2025-04-20 12:00:00'),

(2,  2, 'Beasiswa Anak Yatim Piatu Daerah Terpencil',
    'Pendidikan', 'Nusa Tenggara Timur',
    'Program beasiswa untuk 50 anak yatim piatu di daerah terpencil NTT agar dapat mengenyam pendidikan yang layak hingga jenjang SMA. Dana akan digunakan untuk biaya sekolah, perlengkapan belajar, dan seragam.',
    75000000.00, 58000000.00, '2025-08-31',
    'beasiswa_ntt.jpg',
    'BNI: 9876-5432-1098 a.n. Komunitas Bantu Sesama',
    'aktif', '2025-02-10 09:00:00', '2025-04-18 10:00:00'),

(3,  3, 'Operasi Gratis Bibir Sumbing untuk Anak Kurang Mampu',
    'Kesehatan', 'Surabaya, Jawa Timur',
    'Program operasi bibir sumbing gratis untuk 30 anak dari keluarga tidak mampu. Biaya meliputi operasi, rawat inap, dan pemulihan pasca operasi bersama dokter spesialis bedah plastik rekonstruksi.',
    90000000.00, 90000000.00, '2025-05-31',
    'operasi_bibir.jpg',
    'Mandiri: 1122-3344-5566 a.n. Relawan Harapan Indonesia',
    'selesai', '2025-01-15 10:00:00', '2025-05-30 17:00:00'),

(4,  4, 'Pembangunan Perpustakaan Desa di Garut',
    'Pembangunan Fasilitas', 'Garut, Jawa Barat',
    'Membangun perpustakaan desa yang dilengkapi 500 judul buku dan area baca nyaman untuk warga Desa Sukamaju, Garut. Perpustakaan ini diharapkan menjadi pusat literasi dan belajar masyarakat setempat.',
    40000000.00, 15000000.00, '2025-09-30',
    'perpus_garut.jpg',
    'BCA: 7788-9900-1122 a.n. Yayasan Cahaya Bangsa',
    'aktif', '2025-03-01 08:30:00', '2025-04-15 09:00:00'),

(5,  5, 'Penanaman 10.000 Mangrove di Pesisir Semarang',
    'Lingkungan', 'Semarang, Jawa Tengah',
    'Upaya pemulihan ekosistem pesisir Semarang yang semakin rusak akibat abrasi dan penebangan liar. Dana digunakan untuk pengadaan bibit mangrove, alat tanam, pelatihan relawan, dan pemantauan berkala.',
    30000000.00, 22000000.00, '2025-07-31',
    'mangrove_semarang.jpg',
    'BSI: 3344-5566-7788 a.n. Gerakan Cinta Lingkungan',
    'aktif', '2025-02-10 11:00:00', '2025-04-10 14:00:00'),

(6,  1, 'Tanggap Darurat Gempa Bumi Cianjur',
    'Bencana Alam', 'Cianjur, Jawa Barat',
    'Gempa bumi berkekuatan 5.6 SR mengguncang Cianjur dan merobohkan ratusan rumah warga. Donasi difokuskan untuk pengadaan tenda darurat, logistik, dan pemulihan trauma anak-anak korban bencana.',
    60000000.00, 48000000.00, '2025-06-15',
    'gempa_cianjur.jpg',
    'BRI: 1234-5678-9012 a.n. Yayasan Peduli Nusantara',
    'aktif', '2025-03-10 07:00:00', '2025-04-22 16:00:00'),

(7,  2, 'Sekolah Alam untuk Anak Pedalaman Kalimantan',
    'Pendidikan', 'Kutai Kartanegara, Kalimantan Timur',
    'Mendirikan sekolah alam semi-permanen untuk 80 anak di daerah pedalaman Kalimantan yang tidak terjangkau sekolah negeri. Meliputi pembangunan ruang belajar, pengadaan guru relawan, dan bahan ajar.',
    55000000.00, 12000000.00, '2025-10-31',
    'sekolah_kaltim.jpg',
    'BNI: 9876-5432-1098 a.n. Komunitas Bantu Sesama',
    'nonaktif', '2025-04-01 10:00:00', '2025-04-20 11:00:00');

-- --------------------------------------------------------
-- Data untuk tabel `donasi`
-- --------------------------------------------------------

INSERT INTO `donasi` (`id`, `kampanye_id`, `donatur_id`, `nominal`, `metode_pembayaran`, `pesan_dukungan`, `bukti_transfer`, `status`, `created_at`, `updated_at`) VALUES
-- Kampanye 1: Banjir Kalsel
(1,  1, 1,  500000.00,  'Transfer Bank', 'Semoga cepat pulih, saudara-saudaraku di Kalsel!', 'bukti/donasi_001.jpg', 'verified', '2025-02-05 09:00:00', '2025-02-06 10:00:00'),
(2,  1, 3,  1000000.00, 'Transfer Bank', 'Ikhlas membantu, semoga bermanfaat.', 'bukti/donasi_002.jpg', 'verified', '2025-02-06 10:30:00', '2025-02-07 09:00:00'),
(3,  1, 5,  250000.00,  'QRIS',          'Tetap semangat!', 'bukti/donasi_003.jpg', 'verified', '2025-02-10 11:00:00', '2025-02-11 08:00:00'),
(4,  1, 7,  750000.00,  'Transfer Bank', 'Doaku menyertai kalian.', 'bukti/donasi_004.jpg', 'verified', '2025-02-15 13:00:00', '2025-02-16 10:00:00'),
(5,  1, 9,  500000.00,  'QRIS',          NULL, 'bukti/donasi_005.jpg', 'verified', '2025-03-01 08:00:00', '2025-03-02 09:00:00'),

-- Kampanye 2: Beasiswa NTT
(6,  2, 2,  200000.00,  'Transfer Bank', 'Untuk masa depan anak-anak Indonesia!', 'bukti/donasi_006.jpg', 'verified', '2025-02-12 08:30:00', '2025-02-13 09:00:00'),
(7,  2, 4,  1500000.00, 'Transfer Bank', 'Semoga ilmu yang kalian dapat bermanfaat.', 'bukti/donasi_007.jpg', 'verified', '2025-02-14 10:00:00', '2025-02-15 11:00:00'),
(8,  2, 6,  300000.00,  'QRIS',          'Ayo maju, anak bangsa!', 'bukti/donasi_008.jpg', 'verified', '2025-02-20 09:00:00', '2025-02-21 10:00:00'),
(9,  2, 8,  500000.00,  'Transfer Bank', NULL, 'bukti/donasi_009.jpg', 'verified', '2025-03-02 14:00:00', '2025-03-03 08:00:00'),
(10, 2, 10, 100000.00,  'QRIS',          'Semangat belajar ya adik-adik!', 'bukti/donasi_010.jpg', 'pending', '2025-04-01 11:00:00', '2025-04-01 11:00:00'),

-- Kampanye 3: Operasi Bibir Sumbing (selesai)
(11, 3, 1,  2000000.00, 'Transfer Bank', 'Semoga operasinya lancar dan sehat selalu.', 'bukti/donasi_011.jpg', 'verified', '2025-01-20 08:00:00', '2025-01-21 09:00:00'),
(12, 3, 11, 3000000.00, 'Transfer Bank', 'Bantu sesama adalah ibadah.', 'bukti/donasi_012.jpg', 'verified', '2025-01-22 10:00:00', '2025-01-23 08:00:00'),
(13, 3, 12, 1500000.00, 'QRIS',          'Semoga adik-adik cepat sembuh!', 'bukti/donasi_013.jpg', 'verified', '2025-02-01 09:30:00', '2025-02-02 08:00:00'),

-- Kampanye 4: Perpustakaan Garut
(14, 4, 2,  500000.00,  'Transfer Bank', 'Literasi adalah kunci kemajuan bangsa.', 'bukti/donasi_014.jpg', 'verified', '2025-03-05 08:00:00', '2025-03-06 09:00:00'),
(15, 4, 4,  750000.00,  'Transfer Bank', NULL, 'bukti/donasi_015.jpg', 'verified', '2025-03-10 10:00:00', '2025-03-11 08:00:00'),
(16, 4, 6,  150000.00,  'QRIS',          'Mari kita bangun generasi yang gemar membaca!', 'bukti/donasi_016.jpg', 'pending', '2025-04-18 13:00:00', '2025-04-18 13:00:00'),

-- Kampanye 5: Mangrove Semarang
(17, 5, 3,  300000.00,  'Transfer Bank', 'Jaga bumi, jaga masa depan.', 'bukti/donasi_017.jpg', 'verified', '2025-02-15 09:00:00', '2025-02-16 10:00:00'),
(18, 5, 5,  500000.00,  'QRIS',          'Untuk pantai yang lebih hijau!', 'bukti/donasi_018.jpg', 'verified', '2025-02-20 11:00:00', '2025-02-21 09:00:00'),
(19, 5, 7,  200000.00,  'Transfer Bank', NULL, 'bukti/donasi_019.jpg', 'verified', '2025-03-05 14:00:00', '2025-03-06 10:00:00'),
(20, 5, 9,  1000000.00, 'Transfer Bank', 'Semoga mangrovenya tumbuh subur!', 'bukti/donasi_020.jpg', 'verified', '2025-03-15 08:30:00', '2025-03-16 09:00:00'),

-- Kampanye 6: Gempa Cianjur
(21, 6, 1,  500000.00,  'Transfer Bank', 'Sabar ya saudara-saudaraku di Cianjur.', 'bukti/donasi_021.jpg', 'verified', '2025-03-12 08:00:00', '2025-03-13 09:00:00'),
(22, 6, 2,  1000000.00, 'QRIS',          'Tetap kuat, bantuan sedang datang!', 'bukti/donasi_022.jpg', 'verified', '2025-03-14 10:00:00', '2025-03-15 08:00:00'),
(23, 6, 8,  250000.00,  'Transfer Bank', NULL, 'bukti/donasi_023.jpg', 'verified', '2025-03-20 09:30:00', '2025-03-21 10:00:00'),
(24, 6, 10, 750000.00,  'QRIS',          'Semoga lekas pulih.', 'bukti/donasi_024.jpg', 'verified', '2025-04-01 13:00:00', '2025-04-02 09:00:00'),
(25, 6, 12, 100000.00,  'Transfer Bank', 'Sedikit dari saya, semoga bermanfaat.', 'bukti/donasi_025.jpg', 'rejected', '2025-04-05 10:00:00', '2025-04-06 08:00:00');

COMMIT;
