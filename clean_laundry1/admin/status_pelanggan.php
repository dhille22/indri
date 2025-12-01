<?php
session_start();
if (!isset($_SESSION['admin_id'])) { 
    header('Location: /clean_laundry1/login.php'); 
    exit; 
}

require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/header.php';

$pelanggan_id = (int)($_GET['id'] ?? 0);

if (!$pelanggan_id) {
    header('Location: dashboard_pelanggan.php');
    exit;
}

// Ambil data pelanggan
$pelanggan = $conn->query("SELECT * FROM pelanggan WHERE id_pelanggan = $pelanggan_id")->fetch_assoc();

if (!$pelanggan) {
    echo "<div class='alert alert-danger'>Pelanggan tidak ditemukan</div>";
    require __DIR__ . '/../includes/footer.php';
    exit;
}

// Ambil transaksi terbaru pelanggan
$transaksi = $conn->query("
    SELECT t.*, 
           GROUP_CONCAT(CONCAT(l.nama_layanan, ' (', td.berat, ' kg)') SEPARATOR '<br>') as layanan_detail,
           SUM(td.subtotal) as total_biaya
    FROM transaksi t
    LEFT JOIN transaksi_detail td ON t.id_transaksi = td.id_transaksi
    LEFT JOIN layanan l ON td.id_layanan = l.id_layanan
    WHERE t.id_pelanggan = $pelanggan_id
    GROUP BY t.id_transaksi
    ORDER BY t.tanggal_masuk DESC
");
?>

<div class="container-fluid">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="dashboard_pelanggan.php">Daftar Pelanggan</a></li>
            <li class="breadcrumb-item active" aria-current="page">Status Cucian</li>
        </ol>
    </nav>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Status Cucian Pelanggan</h5>
            <div>
                <a href="tambah_transaksi.php?pelanggan_id=<?= $pelanggan_id ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg"></i> Transaksi Baru
                </a>
                <a href="cetak_status_pelanggan.php?id=<?= $pelanggan_id ?>" 
                   class="btn btn-sm btn-outline-secondary"
                   target="_blank">
                    <i class="bi bi-printer"></i> Cetak
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Nama Pelanggan</th>
                            <td><?= htmlspecialchars($pelanggan['nama_pelanggan']) ?></td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td><?= htmlspecialchars($pelanggan['alamat']) ?></td>
                        </tr>
                        <tr>
                            <th>No. HP</th>
                            <td><?= htmlspecialchars($pelanggan['no_hp'] ?? '-') ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Informasi Cucian</h6>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <div>Total Transaksi:</div>
                            <div><strong><?= $transaksi->num_rows ?> kali</strong></div>
                        </div>
                        <?php 
                        $status_count = [
                            'Diterima' => 0,
                            'Proses' => 0,
                            'Selesai' => 0,
                            'Diambil' => 0
                        ];
                        
                        $transaksi->data_seek(0); // Reset pointer
                        while ($row = $transaksi->fetch_assoc()) {
                            if (isset($status_count[$row['status']])) {
                                $status_count[$row['status']]++;
                            }
                        }
                        $transaksi->data_seek(0); // Reset pointer lagi untuk tampilan selanjutnya
                        
                        foreach ($status_count as $status => $count): 
                            if ($count > 0): 
                                $badge_class = match($status) {
                                    'Selesai' => 'success',
                                    'Diambil' => 'secondary',
                                    'Proses' => 'warning',
                                    default => 'primary'
                                };
                        ?>
                            <div class="d-flex justify-content-between">
                                <div><?= $status ?>:</div>
                                <div><span class="badge bg-<?= $badge_class ?>"><?= $count ?></span></div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>

            <h5 class="mb-3">Riwayat Transaksi</h5>
            
            <?php if ($transaksi->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Tanggal Masuk</th>
                                <th>Layanan</th>
                                <th class="text-end">Total Biaya</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $transaksi->fetch_assoc()): 
                                $badge_class = match($row['status']) {
                                    'Selesai' => 'success',
                                    'Diambil' => 'secondary',
                                    'Proses' => 'warning',
                                    default => 'primary'
                                };
                            ?>
                                <tr>
                                    <td>#<?= str_pad($row['id_transaksi'], 6, '0', STR_PAD_LEFT) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['tanggal_masuk'])) ?></td>
                                    <td>
                                        <small><?= $row['layanan_detail'] ?></small>
                                    </td>
                                    <td class="text-end">Rp <?= number_format($row['total_biaya'], 0, ',', '.') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $badge_class ?>">
                                            <?= $row['status'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="detail_transaksi.php?id=<?= $row['id_transaksi'] ?>" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="cetak_nota.php?id=<?= $row['id_transaksi'] ?>" 
                                           class="btn btn-sm btn-outline-secondary"
                                           target="_blank"
                                           title="Cetak">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Belum ada transaksi untuk pelanggan ini.
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="text-end mb-4">
        <a href="dashboard_pelanggan.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pelanggan
        </a>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
