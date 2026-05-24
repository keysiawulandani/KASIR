# Kasir Blue - Aplikasi Web Kasir Kantin Modern

Aplikasi kasir kantin sekolah berbasis PHP Native dan MySQL dengan desain modern, responsive, dan fitur lengkap.

## Fitur Utama
- Login / register admin dengan session dan validasi
- Navbar responsif modern di semua halaman
- Dashboard statistik produk, transaksi, pendapatan, stok menipis, grafik harian
- Manajemen produk: tambah, edit, hapus, upload gambar, preview gambar, pencarian realtime
- Transaksi POS modern dengan:
  - Katalog produk dengan tombol +/- untuk mengatur qty sebelum add to cart
  - Keranjang belanja dengan tombol +/- untuk edit qty item
  - Level pedas opsional untuk makanan (Tidak Pedas, Sedang, Pedas)
  - Pencarian realtime dan filter kategori
- Metode pembayaran Cash dan QRIS
- Struk otomatis cetak dengan format minimarket/cafe
- Manajemen stok masuk/keluar dan riwayat stok
- Laporan harian/mingguan/bulanan, export Excel, cetak laporan
- Backup database melalui file SQL
- Pengaturan toko: nama, alamat, logo, QRIS
- Keamanan dengan PDO, prepared statement, sanitasi input, session timeout

## Struktur Folder
```
kasir_blue/
├─ assets/
│  ├─ css/style.css
│  ├─ js/app.js
│  └─ images/product-default.svg
├─ inc/
│  ├─ auth.php
│  ├─ config.php
│  ├─ db.php
│  ├─ footer.php
│  ├─ functions.php
│  ├─ header.php
│  ├─ sidebar.php
│  └─ ...
├─ sql/kantin_blue.sql
├─ index.php
├─ register.php
├─ dashboard.php
├─ products.php
├─ product_form.php
├─ save_product.php
├─ delete_product.php
├─ transactions.php
├─ transaction_save.php
├─ receipt.php
├─ stock_history.php
├─ save_stock.php
├─ reports.php
├─ export_report.php
├─ settings.php
├─ save_settings.php
├─ backup.php
├─ logout.php
└─ README.md
```

## Cara Instalasi di XAMPP
1. Copy folder `kasir_blue` ke `C:\xampp\htdocs\`
2. Jalankan Apache dan MySQL di XAMPP Control Panel
3. Buka `http://localhost/phpmyadmin`
4. Buat database baru dengan nama `kasir_blue`
5. Import file SQL di `sql/kantin_blue.sql`
6. Buka `http://localhost/kasir_blue`

## Cara Import Database
1. Masuk ke phpMyAdmin
2. Pilih database `kasir_blue`
3. Klik tab `Import`
4. Pilih file `sql/kantin_blue.sql`
5. Klik `Go`

## Login Admin Demo
- NISN/NIK: `1234567890`
- Password: `admin123`

## Cara Menerapkan Fitur Level Pedas (Opsional)

1. Buka phpMyAdmin
2. Pilih database `kasir_blue`
3. Klik tab `SQL`
4. Paste kode dari file `sql/add_level_pedas.sql`:
```sql
ALTER TABLE products ADD COLUMN level_pedas VARCHAR(20) DEFAULT NULL;
UPDATE products SET level_pedas = 'Pedas' WHERE nama_produk IN ('Nasi Goreng', 'Ayam Geprek', 'Seblak');
UPDATE products SET level_pedas = 'Sedang' WHERE nama_produk IN ('Mie Goreng', 'Bakso');
```
5. Klik `Go`
6. Sekarang halaman transaksi akan menampilkan level pedas untuk produk makanan

## Penjelasan Fitur
- `index.php`: Halaman login dengan validasi dan sesi
- `register.php`: Pendaftaran admin pertama kali jika belum ada akun
- `dashboard.php`: Menampilkan ringkasan produk, transaksi, pendapatan, stok menipis, grafik penjualan, dan jam realtime
- `products.php`: Daftar produk dalam bentuk kartu, pencarian realtime, tombol edit/hapus
- `product_form.php`: Form tambah/edit produk lengkap dengan upload gambar
- `transactions.php`: 
  - POS modern dengan katalog produk dan keranjang
  - **Tombol +/- di katalog**: Atur jumlah sebelum add to cart (misal: Jus Jeruk 2, bukan 1)
  - **Tombol +/- di keranjang**: Edit qty item dalam keranjang
  - Level pedas untuk makanan (ditampilkan jika sudah apply SQL)
  - Pajak, diskon, dan pembayaran cash/QRIS
- `transaction_save.php`: Menyimpan transaksi, mengurangi stok, menyimpan detail pembayaran, dan mencatat history
- `receipt.php`: Struk pembelian otomatis siap dicetak
- `stock_history.php`: Form stok masuk/keluar dan riwayat transaksi stok
- `save_stock.php`: Logika update stok dan history stok
- `reports.php`: Filter laporan berdasarkan tanggal dan ringkasan
- `export_report.php`: Export laporan ke Excel atau tampilan print untuk PDF
- `settings.php`: Upload logo, QRIS, dan atur nama/alamat toko
- `backup.php`: Download file SQL backup

## Catatan
- Pastikan folder `uploads/` dapat ditulis oleh web server
- Jika ingin styling lebih kuat, modifikasi `assets/css/style.css`
- QRIS akan tampil jika Anda upload gambar QRIS di halaman Pengaturan

Selamat menggunakan aplikasi kasir modern untuk presentasi sekolah!
