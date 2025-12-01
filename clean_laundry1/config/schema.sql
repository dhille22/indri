-- Create database (adjust if needed)
CREATE DATABASE db_clean_laundry;
USE db_clean_laundry;

-- Tabel Admin
CREATE TABLE admin (
  id_admin INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL
);

-- Tabel Pelanggan
CREATE TABLE pelanggan (
  id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
  nama_pelanggan VARCHAR(100) NOT NULL,
  alamat TEXT,
  no_hp VARCHAR(20)
);

-- Tabel Jenis Layanan
CREATE TABLE layanan (
  id_layanan INT AUTO_INCREMENT PRIMARY KEY,
  nama_layanan VARCHAR(100) NOT NULL,
  harga_perkg DOUBLE NOT NULL
);

-- Tabel Transaksi
CREATE TABLE transaksi (
  id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
  id_pelanggan INT NOT NULL,
  id_layanan INT NOT NULL,
  tanggal_masuk DATE,
  tanggal_selesai DATE,
  berat_cucian DOUBLE,
  total_biaya DOUBLE,
  status VARCHAR(20) DEFAULT 'Proses',
  FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan),
  FOREIGN KEY (id_layanan) REFERENCES layanan(id_layanan)
);

-- Tabel Laporan
CREATE TABLE laporan (
  id_laporan INT AUTO_INCREMENT PRIMARY KEY,
  total_transaksi INT,
  total_pendapatan DOUBLE,
  periode VARCHAR(50)
);
