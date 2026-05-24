-- Database: kasir_blue
CREATE DATABASE IF NOT EXISTS `kasir_blue` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `kasir_blue`;

DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS stock_history;
DROP TABLE IF EXISTS transaction_details;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS suppliers;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS settings;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nisn VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'admin',
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE suppliers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    email VARCHAR(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_produk VARCHAR(50) NOT NULL UNIQUE,
    nama_produk VARCHAR(150) NOT NULL,
    deskripsi_produk TEXT NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    supplier_id INT UNSIGNED NOT NULL,
    stok INT UNSIGNED NOT NULL DEFAULT 0,
    minimal_stok INT UNSIGNED NOT NULL DEFAULT 5,
    unit VARCHAR(30) NOT NULL DEFAULT 'pcs',
    harga_beli INT UNSIGNED NOT NULL DEFAULT 0,
    harga_jual INT UNSIGNED NOT NULL DEFAULT 0,
    gambar_produk VARCHAR(255) DEFAULT NULL,
    tanggal_ditambahkan DATE NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_transaksi VARCHAR(80) NOT NULL UNIQUE,
    user_id INT UNSIGNED NOT NULL,
    buyer_name VARCHAR(150) NOT NULL DEFAULT 'Umum',
    total_item INT UNSIGNED NOT NULL,
    subtotal BIGINT UNSIGNED NOT NULL,
    pajak BIGINT UNSIGNED NOT NULL,
    diskon BIGINT UNSIGNED NOT NULL,
    total_bayar BIGINT UNSIGNED NOT NULL,
    bayar BIGINT UNSIGNED NOT NULL,
    kembalian BIGINT UNSIGNED NOT NULL,
    metode_pembayaran VARCHAR(30) NOT NULL,
    qris_type VARCHAR(30) DEFAULT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'PENDING',
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE transaction_details (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    qty INT UNSIGNED NOT NULL,
    harga BIGINT UNSIGNED NOT NULL,
    subtotal BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE stock_history (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    type ENUM('IN','OUT') NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    note VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT UNSIGNED NOT NULL,
    method VARCHAR(30) NOT NULL,
    amount BIGINT UNSIGNED NOT NULL,
    status VARCHAR(30) NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO categories (name) VALUES
('Makanan'),
('Minuman'),
('Snack'),
('Dessert');

INSERT INTO suppliers (name, phone, email) VALUES
('Supplier Bhakti', '081234567890', 'supplier1@example.com'),
('Supplier Santai', '082112345678', 'supplier2@example.com');

INSERT INTO users (nisn, password, name, role, created_at) VALUES
('1234567890', '$2y$10$rHo7ePk7zPPoJAP5nNYZ/.DsCu4O0whpLfhub57zNAIJ/cGmWsWvC', 'Admin Kantin', 'admin', NOW());

INSERT INTO products (kode_produk, nama_produk, deskripsi_produk, category_id, supplier_id, stok, minimal_stok, unit, harga_beli, harga_jual, gambar_produk, tanggal_ditambahkan) VALUES
('KNT001', 'Nasi Goreng', 'Nasi goreng spesial dengan telur dan saus rahasia.', 1, 1, 25, 5, 'porsi', 12000, 20000, NULL, NOW()),
('KNT002', 'Ayam Geprek', 'Ayam geprek pedas dengan sambal ulek segar.', 1, 1, 20, 5, 'porsi', 15000, 26000, NULL, NOW()),
('KNT003', 'Mie Goreng', 'Mie goreng dengan sayuran dan topping bakso.', 1, 1, 18, 4, 'porsi', 10000, 18000, NULL, NOW()),
('KNT004', 'Roti Bakar Keju', 'Roti bakar lembut dengan keju leleh dan cokelat.', 4, 2, 30, 8, 'pcs', 7000, 13000, NULL, NOW()),
('KNT005', 'Seblak', 'Seblak kuah pedas dengan bakso dan telur.', 1, 1, 22, 5, 'porsi', 11000, 21000, NULL, NOW()),
('KNT006', 'Bakso', 'Bakso sapi dengan kuah gurih dan pangsit.', 1, 2, 26, 6, 'porsi', 13000, 23000, NULL, NOW()),
('KNT007', 'Es Teh', 'Es teh manis dingin segar untuk berbuka.', 2, 2, 40, 10, 'gelas', 3000, 7000, NULL, NOW()),
('KNT008', 'Jus Jeruk', 'Jus jeruk segar penuh vitamin C.', 2, 2, 28, 8, 'gelas', 5000, 12000, NULL, NOW()),
('KNT009', 'Kopi Susu', 'Kopi susu hangat dengan foam lembut.', 2, 2, 20, 6, 'gelas', 7000, 15000, NULL, NOW()),
('KNT010', 'Air Mineral', 'Air mineral botol isi 600ml.', 2, 2, 50, 10, 'botol', 2000, 5000, NULL, NOW());

INSERT INTO settings (`key`, value) VALUES
('store_name', 'KANTIN BLUE'),
('store_address', 'Jl. Cibadak, Gg. Sereh 26 Bandung'),
('qris_image', ''),
('store_logo', '');
