<?php
session_start();
require __DIR__ . '/../config/database.php';

// Jika sudah login, redirect ke halaman status
if (isset($_SESSION['pelanggan_id'])) {
    header('Location: status.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $no_hp = $conn->real_escape_string($_POST['no_hp'] ?? '');
    $kode_transaksi = $conn->real_escape_string($_POST['kode_transaksi'] ?? '');
    
    // Cari transaksi berdasarkan nomor HP dan kode transaksi
    $query = "SELECT t.*, p.nama_pelanggan, p.no_hp 
              FROM transaksi t 
              JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
              WHERE p.no_hp = '$no_hp' AND t.id_transaksi = '$kode_transaksi'";
    
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $transaksi = $result->fetch_assoc();
        $_SESSION['pelanggan_id'] = $transaksi['id_pelanggan'];
        $_SESSION['pelanggan_nama'] = $transaksi['nama_pelanggan'];
        $_SESSION['transaksi_id'] = $transaksi['id_transaksi'];
        
        header('Location: status.php');
        exit;
    } else {
        $error = 'Nomor HP atau Kode Transaksi tidak valid';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pelanggan - Clean Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h1 {
            color: #0d6efd;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="logo">
                <h1>Clean Laundry</h1>
                <p class="text-muted">Masuk untuk melihat status cucian Anda</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="mb-3">
                    <label for="no_hp" class="form-label">Nomor HP</label>
                    <input type="text" class="form-control" id="no_hp" name="no_hp" required 
                           placeholder="Contoh: 081234567890" pattern="[0-9]+" title="Hanya angka yang diperbolehkan">
                </div>
                <div class="mb-3">
                    <label for="kode_transaksi" class="form-label">Kode Transaksi</label>
                    <input type="text" class="form-control" id="kode_transaksi" name="kode_transaksi" required
                           placeholder="Masukkan kode transaksi">
                </div>
                <div class="d-grid">
                    <button type="submit" name="login" class="btn btn-primary">Masuk</button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <a href="../index.php" class="text-decoration-none">← Kembali ke halaman utama</a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
