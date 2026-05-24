-- Tambahkan kolom level_pedas ke tabel products (opsional)
ALTER TABLE products ADD COLUMN level_pedas VARCHAR(20) DEFAULT NULL;

-- Update produk makanan dengan level pedas
UPDATE products SET level_pedas = 'Pedas' WHERE nama_produk IN ('Nasi Goreng', 'Ayam Geprek', 'Seblak');
UPDATE products SET level_pedas = 'Sedang' WHERE nama_produk IN ('Mie Goreng', 'Bakso');
