# BantuSesama — Mini Project #2 (Website Dinamis)

Lanjutan dari Mini Project #1. Website statis diubah menjadi dinamis menggunakan **PHP** dan **MySQL**.

---

## Anggota Kelompok

| NIM | Nama |
|---|---|
| 71241126 | Valentino Kevin Yulianto |
| 71241133 | Irene Fernanda Putri |
| 71241154 | Indira Mai Narwastu |

---

## Teknologi

- PHP, MySQL, HTML, CSS

---

## Fitur yang Dibutuhkan

### 1. Halaman Utama
- Daftar kampanye diambil dari database (bukan statis)
- Hanya tampilkan kampanye yang belum melewati batas waktu
- Fungsi search berdasarkan judul, kategori, dan lokasi
- Diurutkan berdasarkan deadline terdekat dan dana terkecil
- Pagination

### 2. Halaman Detail Kampanye
- Data diambil dari database sesuai kampanye yang diklik
- Terdapat progress bar dana terkumpul
- Tombol "Donasi Sekarang"

### 3. Halaman Donasi
- Wajib login dulu, jika belum login diarahkan ke halaman login
- Menampilkan ringkasan kampanye dan data diri donatur dari database
- Input: nominal donasi, metode pembayaran, pesan dukungan (opsional), bukti transfer (JPG/PNG/PDF)
- Validasi nominal minimal Rp10.000
- Donasi tersimpan dengan status **PENDING**

### 4. Login & Logout
- Form login dengan email dan password
- Validasi dari database, simpan ke session
- Tombol login berubah jadi logout setelah masuk
- Akses tanpa login di-redirect ke halaman login

### 5. Pengelolaan Kampanye (Penyelenggara)
- CRUD kampanye
- Kampanye dengan dana terkumpul ≥ Rp10.000 tidak bisa dihapus
- Lihat daftar donatur per kampanye
- Verifikasi donasi: ubah status pending → verified / rejected
  - Verified → dana terkumpul bertambah
  - Rejected → dana tidak berubah

### 6. Riwayat Donasi (Donatur)
- Donatur bisa melihat riwayat donasi yang pernah dilakukan

---

## Bonus (Nilai Tambah)
- Ringkasan total donasi per status (verified / pending / rejected)
- Indikator warna status donasi (hijau / kuning / merah)
