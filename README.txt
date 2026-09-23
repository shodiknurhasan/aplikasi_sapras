SIGAP SARPRAS - PHP Native OOP MVC
================================

CARA MENJALANKAN (XAMPP)
1. Pastikan XAMPP Apache + MySQL aktif.
2. Salin folder SIGAP_SARPRAS ke:
   C:\xampp\htdocs\
3. Buka phpMyAdmin, lalu import file database.sql (file ini sudah membuat
   database Pengaduan_Sarana beserta semua tabel dan data contoh -
   tidak perlu membuat database manual lagi).
4. Cek config/koneksi.php:
   host=localhost
   user=root
   pass=
   db=Pengaduan_Sarana
5. Buka:
   http://localhost/SIGAP_SARPRAS/

LOGIN DEMO
Admin:
username: admin_sarpras
password: admin123

Siswa (semua akun contoh pakai password yang sama):
username: siswa_andi / siswa_budi / siswa_rizky / siswa_dinda / siswa_fajar
password: siswa123

Untuk membuat akun siswa baru, login sebagai admin lalu buka menu Data Siswa.

FITUR
- Login role admin dan siswa
- Dashboard siswa & dashboard admin
- CRUD Siswa, CRUD Kategori
- Buat pengaduan (dengan upload foto opsional)
- Daftar pengaduan (siswa: Riwayat Pengaduan, admin: Semua Pengaduan)
- Tanggapan admin ke siswa + riwayat tanggapan untuk siswa
- Detail pengaduan + timeline penanganan
- Update status: Diverifikasi, Diproses, Selesai, Ditolak
- Laporan ringkasan (per status & per kategori)
- Pengaturan akun admin (ubah nama & password)

CATATAN PERBAIKAN DARI VERSI SEBELUMNYA
- File config/layout.php ditambahkan: berisi fungsi sidebar() yang
  sebelumnya dipanggil di semua halaman tapi tidak pernah didefinisikan
  di file manapun, sehingga aplikasi crash (Fatal Error) di setiap
  halaman setelah login.
- database.sql (baru) menggantikan database_admin_demo.sql yang lama.
  File lama hanya berisi 1 baris UPDATE password tanpa ada satupun
  CREATE TABLE, sehingga aplikasi tidak mungkin bisa konek ke database.
  database.sql sekarang berisi skema lengkap + data contoh.
- Menu sidebar yang ada di desain tapi belum ada halamannya sudah dibuat:
  views/semua_pengaduan.php, views/laporan.php, views/pengaturan.php
  (admin), views/riwayat.php, views/siswa_tanggapan.php (siswa).
- Upload foto pada form pengaduan sebelumnya hanya tampilan (tidak
  fungsional, tidak ada atribut name). Sekarang benar-benar menyimpan
  file ke assets/uploads/ dan ditampilkan di halaman detail/tanggapan.
- Tombol aksi pada Data Siswa disesuaikan dengan desain (Edit + HAPUS).

STRUKTUR DATABASE
admin, siswa, kategori, pengaduan, penanganan - lihat database.sql
untuk detail kolom dan relasi foreign key.

PEMBARUAN DESAIN
- Seluruh halaman menggunakan sistem desain yang lebih konsisten.
- Warna, radius, spacing, tombol, badge status, tabel, form, dan kartu diseragamkan.
- Sidebar dan profil dibuat lebih rapi serta menampilkan inisial pengguna.
- Avatar pada header mengikuti inisial nama pengguna yang sedang login.
- Tampilan mobile diperbaiki agar navigasi dan tabel lebih nyaman digunakan.
- Inline CSS pada halaman PHP dihapus dan dipusatkan ke assets/style.css.
- Ditambahkan komponen reusable untuk alert, empty state, feedback, detail foto, dan toolbar.
- Tidak mengubah struktur database maupun alur utama fitur aplikasi.
