<?php
// Koneksi ke database
require __DIR__ . '/config/database.php';

// Pesan untuk menampilkan hasil
$messages = [];

try {
    // Periksa apakah kolom total_biaya sudah ada
    $check = $conn->query("SHOW COLUMNS FROM transaksi LIKE 'total_biaya'");
    
    if ($check->num_rows === 0) {
        // Jika kolom belum ada, tambahkan
        $sql = "ALTER TABLE transaksi ADD COLUMN total_biaya DECIMAL(12,2) DEFAULT 0.00";
        if ($conn->query($sql) === TRUE) {
            $messages[] = "Kolom 'total_biaya' berhasil ditambahkan ke tabel 'transaksi'.";
        } else {
            throw new Exception("Gagal menambahkan kolom: " . $conn->error);
        }
    } else {
        $messages[] = "Kolom 'total_biaya' sudah ada dalam tabel 'transaksi'.";
    }
    
    // Periksa apakah tabel transaksi_detail sudah ada
    $check_table = $conn->query("SHOW TABLES LIKE 'transaksi_detail'");
    
    if ($check_table->num_rows === 0) {
        // Buat tabel transaksi_detail jika belum ada
        $sql = "CREATE TABLE transaksi_detail (
            id_detail INT AUTO_INCREMENT PRIMARY KEY,
            id_transaksi INT,
            id_layanan INT,
            berat DECIMAL(10,2),
            subtotal DECIMAL(12,2),
            FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi) ON DELETE CASCADE,
            FOREIGN KEY (id_layanan) REFERENCES layanan(id_layanan)
        )";
        
        if ($conn->query($sql) === TRUE) {
            $messages[] = "Tabel 'transaksi_detail' berhasil dibuat.";
        } else {
            throw new Exception("Gagal membuat tabel: " . $conn->error);
        }
    } else {
        $messages[] = "Tabel 'transaksi_detail' sudah ada.";
    }
    
    // Periksa apakah kolom no_hp sudah ada di tabel pelanggan
    $check_hp = $conn->query("SHOW COLUMNS FROM pelanggan LIKE 'no_hp'");
    
    if ($check_hp->num_rows === 0) {
        // Tambahkan kolom no_hp jika belum ada
        $sql = "ALTER TABLE pelanggan ADD COLUMN no_hp VARCHAR(20) AFTER alamat";
        if ($conn->query($sql) === TRUE) {
            $messages[] = "Kolom 'no_hp' berhasil ditambahkan ke tabel 'pelanggan'.";
        } else {
            throw new Exception("Gagal menambahkan kolom no_hp: " . $conn->error);
        }
    } else {
        $messages[] = "Kolom 'no_hp' sudah ada dalam tabel 'pelanggan'.";
    }
    
} catch (Exception $e) {
    $messages[] = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Database - Clean Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
        }
        .message-box {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 5px;
            background-color: #f8f9fa;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="message-box">
            <h2 class="mb-4">Update Database</h2>
            <?php foreach ($messages as $message): ?>
                <div class="alert <?= strpos($message, 'Error:') === 0 ? 'alert-danger' : 'alert-success' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endforeach; ?>
            
            <?php if (!in_array(false, array_map(function($msg) { return strpos($msg, 'Error:') === false; }, $messages))): ?>
                <div class="alert alert-info mt-4">
                    <strong>Update berhasil!</strong> Anda sekarang dapat menggunakan fitur transaksi dengan banyak layanan.
                </div>
                <div class="mt-4">
                    <a href="admin/transaksi_baru.php" class="btn btn-primary">Ke Halaman Transaksi Baru</a>
                    <a href="index.php" class="btn btn-secondary">Kembali ke Beranda</a>
                </div>
            <?php else: ?>
                <div class="mt-4">
                    <a href="index.php" class="btn btn-primary">Kembali ke Beranda</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
