<?php
session_start();
require __DIR__ . '/config/database.php';

$error = '';
$transaksi = null;
$pelanggan = null;

// Proses form pencarian
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cari'])) {
    $no_hp = trim($_POST['no_hp'] ?? '');
    $nama_pelanggan = trim($_POST['nama_pelanggan'] ?? '');
    
    if (empty($no_hp) || empty($nama_pelanggan)) {
        $error = 'Mohon isi nomor HP dan nama pelanggan';
    } else {
        // Cari transaksi berdasarkan nomor HP dan nama pelanggan
        $query = "SELECT t.*, p.* 
                 FROM transaksi t 
                 JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                 WHERE p.no_hp = ? AND p.nama_pelanggan LIKE ?
                 ORDER BY t.tanggal_masuk DESC";
        
        $stmt = $conn->prepare($query);
        $nama_pelanggan_like = "%$nama_pelanggan%";
        $stmt->bind_param('ss', $no_hp, $nama_pelanggan_like);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $transaksi = $result->fetch_assoc();
            $pelanggan = [
                'id_pelanggan' => $transaksi['id_pelanggan'],
                'nama_pelanggan' => $transaksi['nama_pelanggan'],
                'no_hp' => $transaksi['no_hp'],
                'alamat' => $transaksi['alamat']
            ];
            
            // Ambil detail layanan
            $detail_query = "SELECT td.*, l.nama_layanan, l.harga_perkg 
                            FROM transaksi_detail td
                            JOIN layanan l ON td.id_layanan = l.id_layanan
                            WHERE td.id_transaksi = ?";
            $stmt = $conn->prepare($detail_query);
            $stmt->bind_param('i', $kode_transaksi);
            $stmt->execute();
            $detail_result = $stmt->get_result();
            $detail_layanan = [];
            
            while ($row = $detail_result->fetch_assoc()) {
                $detail_layanan[] = $row;
            }
            
            $transaksi['detail_layanan'] = $detail_layanan;
        } else {
            $error = 'Data tidak ditemukan. Pastikan nomor HP dan kode transaksi benar.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Cucian - Clean Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .status-card {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .status-header {
            background: #0d6efd;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .status-body {
            padding: 20px;
        }
        .status-step {
            display: flex;
            margin-bottom: 20px;
            position: relative;
            padding-left: 50px;
        }
        .status-step:before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: -20px;
            width: 2px;
            background: #dee2e6;
        }
        .status-step:last-child:before {
            display: none;
        }
        .status-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            left: 0;
            top: 0;
        }
        .status-step.active .status-icon {
            background: #0d6efd;
            color: white;
        }
        .status-step.completed .status-icon {
            background: #198754;
            color: white;
        }
        .status-details {
            flex: 1;
        }
        .status-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .status-date {
            font-size: 0.85em;
            color: #6c757d;
        }
        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .search-box {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        @media print {
            .no-print, .search-box {
                display: none !important;
            }
            body {
                background: white;
            }
            .status-card {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="text-primary">Clean Laundry</h1>
            <p class="lead">Cek Status Cucian Anda</p>
        </div>
        
        <div class="search-box mb-5">
            <h4 class="text-center mb-4">Cari Status Cucian</h4>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="post" class="g-3">
                <div class="mb-3">
                    <label for="no_hp" class="form-label">Nomor HP</label>
                    <input type="text" class="form-control form-control-lg" id="no_hp" name="no_hp" 
                           placeholder="Contoh: 081234567890" required
                           value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>">
                    <div class="form-text">Masukkan nomor HP yang terdaftar</div>
                </div>
                
                <div class="mb-4">
                    <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
                    <input type="text" class="form-control form-control-lg" id="nama_pelanggan" 
                           name="nama_pelanggan" placeholder="Masukkan nama lengkap" required
                           value="<?= htmlspecialchars($_POST['nama_pelanggan'] ?? '') ?>">
                    <div class="form-text">Masukkan nama sesuai yang terdaftar</div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" name="cari" class="btn btn-primary btn-lg">
                        <i class="bi bi-search"></i> Cek Status
                    </button>
                </div>
            </form>
        </div>
        
        <?php if ($transaksi && $pelanggan): ?>
        <div class="status-card">
            <div class="status-header">
                <h3 class="mb-0">Status Cucian</h3>
                <div class="text-white-50">Kode Transaksi: #<?= str_pad($transaksi['id_transaksi'], 6, '0', STR_PAD_LEFT) ?></div>
            </div>
            
            <div class="status-body">
                <div class="mb-4">
                    <h5>Detail Pelanggan</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Nama:</strong> <?= htmlspecialchars($pelanggan['nama_pelanggan']) ?></p>
                            <p class="mb-1"><strong>No. HP:</strong> <?= htmlspecialchars($pelanggan['no_hp']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Tanggal Masuk:</strong> <?= date('d/m/Y H:i', strtotime($transaksi['tanggal_masuk'])) ?></p>
                            <p class="mb-1"><strong>Estimasi Selesai:</strong> 
                                <?= $transaksi['tanggal_selesai'] 
                                    ? date('d/m/Y', strtotime($transaksi['tanggal_selesai'])) 
                                    : 'Dalam proses' 
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h5>Detail Layanan</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Layanan</th>
                                    <th class="text-end">Berat (kg)</th>
                                    <th class="text-end">Harga/kg</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_biaya = 0;
                                $no = 1;
                                foreach ($transaksi['detail_layanan'] as $layanan): 
                                    $total_biaya += $layanan['subtotal'];
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($layanan['nama_layanan']) ?></td>
                                    <td class="text-end"><?= number_format($layanan['berat'], 2) ?></td>
                                    <td class="text-end">Rp <?= number_format($layanan['harga_perkg'], 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format($layanan['subtotal'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <!-- Total -->
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total</td>
                                    <td class="text-end fw-bold">Rp <?= number_format($total_biaya, 0, ',', '.') ?></td>
                                </tr>
                                <!-- Pembayaran -->
                                <?php if (isset($transaksi['total_bayar']) && $transaksi['total_bayar'] > 0): ?>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Dibayar</td>
                                    <td class="text-end fw-bold">Rp <?= number_format($transaksi['total_bayar'], 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Kembalian</td>
                                    <td class="text-end fw-bold">Rp <?= number_format(($transaksi['total_bayar'] - $total_biaya), 0, ',', '.') ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <h5>Status Pengerjaan</h5>
                <div class="status-timeline">
                    <?php
                    $status_list = [
                        'Diterima' => 'Cucian telah diterima',
                        'Proses' => 'Cucian sedang dicuci',
                        'Selesai' => 'Cucian telah selesai',
                        'Diambil' => 'Cucian telah diambil'
                    ];
                    
                    $current_status = $transaksi['status'];
                    $status_found = false;
                    
                    foreach ($status_list as $status => $description) {
                        $is_active = $status === $current_status;
                        $is_completed = array_search($status, array_keys($status_list)) < 
                                      array_search($current_status, array_keys($status_list));
                        
                        if ($is_active) $status_found = true;
                        
                        echo '<div class="status-step ' . ($is_active ? 'active' : '') . ($is_completed ? ' completed' : '') . '">';
                        echo '  <div class="status-icon">';
                        if ($is_completed) {
                            echo '<i class="bi bi-check-lg"></i>';
                        } else {
                            echo '<i class="bi ' . ($is_active ? 'bi-arrow-right' : 'bi-dot') . '"></i>';
                        }
                        echo '  </div>';
                        echo '  <div class="status-details">';
                        echo '    <div class="status-title">' . $status . '</div>';
                        echo '    <div class="status-date">' . $description . '</div>';
                        
                        // Tampilkan tanggal untuk status yang sudah terlewati
                        if ($is_completed) {
                            $date_field = '';
                            switch($status) {
                                case 'Diterima':
                                    $date_field = 'tanggal_masuk';
                                    break;
                                case 'Proses':
                                    $date_field = 'tanggal_proses';
                                    break;
                                case 'Selesai':
                                    $date_field = 'tanggal_selesai';
                                    break;
                                case 'Diambil':
                                    $date_field = 'tanggal_diambil';
                                    break;
                            }
                            
                            if (!empty($transaksi[$date_field])) {
                                echo '<div class="status-date">' . date('d/m/Y H:i', strtotime($transaksi[$date_field])) . '</div>';
                            }
                        }
                        
                        echo '  </div>';
                        echo '</div>';
                    }
                    
                    // Jika status tidak ada di daftar, tampilkan status custom
                    if (!$status_found && !empty($current_status)) {
                        echo '<div class="status-step active">';
                        echo '  <div class="status-icon"><i class="bi bi-info-circle"></i></div>';
                        echo '  <div class="status-details">';
                        echo '    <div class="status-title">' . htmlspecialchars($current_status) . '</div>';
                        echo '    <div class="status-date">Status saat ini</div>';
                        echo '  </div>';
                        echo '</div>';
                    }
                    ?>
                </div>
                
                <?php if (!empty($transaksi['keterangan'])): ?>
                <div class="alert alert-info mt-4">
                    <strong>Catatan:</strong> <?= nl2br(htmlspecialchars($transaksi['keterangan'])) ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="p-3 border-top text-center text-muted">
                <small>Terima kasih telah menggunakan layanan kami. Untuk informasi lebih lanjut, hubungi kami di nomor yang tertera.</small>
            </div>
        </div>
        
        <div class="text-center mt-4 mb-5">
            <a href="status_cucian.php" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Kembali ke Pencarian
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Cetak Halaman
            </button>
        </div>
        <?php endif; ?>
    </div>
    
    <footer class="bg-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">
                <strong>Clean Laundry</strong> - Jl. Contoh No. 123, Kota Anda<br>
                <i class="bi bi-telephone"></i> (021) 12345678 | 
                <i class="bi bi-whatsapp"></i> 081234567890
            </p>
            <p class="text-muted mt-2 mb-0">
                &copy; <?= date('Y') ?> Clean Laundry. Semua hak dilindungi.
            </p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Format input nomor HP (hanya angka)
    document.getElementById('no_hp').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        e.target.value = value;
    });
    
    // Format input nama (hanya huruf dan spasi)
    document.getElementById('nama_pelanggan').addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^a-zA-Z\s]/g, '');
        e.target.value = value;
    });
    </script>
</body>
</html>
