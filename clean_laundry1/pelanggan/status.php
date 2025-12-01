<?php
session_start();
require __DIR__ . '/../config/database.php';

// Jika belum login, redirect ke halaman login
if (!isset($_SESSION['pelanggan_id'])) {
    header('Location: index.php');
    exit;
}

$pelanggan_id = $_SESSION['pelanggan_id'];
$transaksi_id = $_SESSION['transaksi_id'] ?? null;

// Ambil data transaksi pelanggan
$query = "SELECT t.*, p.nama_pelanggan, p.alamat, p.no_hp, 
          l.nama_layanan, td.berat, td.subtotal
          FROM transaksi t
          JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
          LEFT JOIN transaksi_detail td ON t.id_transaksi = td.id_transaksi
          LEFT JOIN layanan l ON td.id_layanan = l.id_layanan
          WHERE t.id_pelanggan = ? AND t.id_transaksi = ?
          ORDER BY t.tanggal_masuk DESC, td.id_detail";

$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $pelanggan_id, $transaksi_id);
$stmt->execute();
$result = $stmt->get_result();

// Jika tidak ada data, redirect ke halaman login
if ($result->num_rows === 0) {
    session_destroy();
    header('Location: index.php?error=not_found');
    exit;
}

// Ambil data pertama untuk header
$transaksi = $result->fetch_assoc();
$result->data_seek(0); // Reset pointer ke awal hasil query
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Cucian - Clean Laundry</title>
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
        @media print {
            .no-print {
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
    <div class="container">
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
                            <p class="mb-1"><strong>Nama:</strong> <?= htmlspecialchars($transaksi['nama_pelanggan']) ?></p>
                            <p class="mb-1"><strong>No. HP:</strong> <?= htmlspecialchars($transaksi['no_hp']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Tanggal Masuk:</strong> <?= date('d/m/Y', strtotime($transaksi['tanggal_masuk'])) ?></p>
                            <p class="mb-1"><strong>Estimasi Selesai:</strong> 
                                <?= $transaksi['tanggal_selesai'] 
                                    ? date('d/m/Y', strtotime($transaksi['tanggal_selesai'])) 
                                    : 'Dalam proses' 
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <h5>Detail Layanan</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Layanan</th>
                                <th class="text-end">Berat (kg)</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total = 0;
                            while ($row = $result->fetch_assoc()): 
                                $total += $row['subtotal'];
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_layanan']) ?></td>
                                <td class="text-end"><?= number_format($row['berat'], 2, ',', '.') ?></td>
                                <td class="text-end">Rp <?= number_format($row['subtotal'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <tr>
                                <th colspan="2" class="text-end">Total</th>
                                <th class="text-end">Rp <?= number_format($total, 0, ',', '.') ?></th>
                            </tr>
                        </tbody>
                    </table>
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
            </div>
            
            <div class="p-3 border-top text-center text-muted">
                <small>Terima kasih telah menggunakan layanan kami. Untuk informasi lebih lanjut, hubungi kami di nomor yang tertera.</small>
            </div>
        </div>
    </div>
    
    <button onclick="window.print()" class="btn btn-primary print-btn no-print" title="Cetak">
        <i class="bi bi-printer"></i>
    </button>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
