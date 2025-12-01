-- Buat tabel transaksi_detail untuk menyimpan detail layanan dalam satu transaksi
CREATE TABLE IF NOT EXISTS transaksi_detail (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi INT,
    id_layanan INT,
    berat DECIMAL(10,2),
    subtotal DECIMAL(12,2),
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi) ON DELETE CASCADE,
    FOREIGN KEY (id_layanan) REFERENCES layanan(id_layanan)
);

-- Tambahkan kolom total_biaya di tabel transaksi jika belum ada
ALTER TABLE transaksi ADD COLUMN IF NOT EXISTS total_biaya DECIMAL(12,2) DEFAULT 0.00;

-- Buat tabel untuk login pelanggan
CREATE TABLE IF NOT EXISTS pelanggan_login (
    id_login INT AUTO_INCREMENT PRIMARY KEY,
    id_pelanggan INT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    token VARCHAR(255),
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan) ON DELETE CASCADE,
    UNIQUE KEY unique_username (username)
);

-- Tambahkan kolom no_hp jika belum ada di tabel pelanggan
ALTER TABLE pelanggan ADD COLUMN IF NOT EXISTS no_hp VARCHAR(20) AFTER alamat;
