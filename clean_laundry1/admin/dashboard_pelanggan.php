<?php
session_start();
if (!isset($_SESSION['admin_id'])) { 
    header('Location: /clean_laundry1/login.php'); 
    exit; 
}

require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/header.php';

// Ambil daftar pelanggan beserta status terakhir transaksinya
$query = "SELECT 
            p.id_pelanggan, 
            p.nama_pelanggan, 
            p.no_hp,
            MAX(t.tanggal_masuk) as tanggal_terakhir,
            t.status as status_terakhir,
            COUNT(t.id_transaksi) as total_transaksi
          FROM pelanggan p
          LEFT JOIN transaksi t ON p.id_pelanggan = t.id_pelanggan
          GROUP BY p.id_pelanggan
          ORDER BY p.nama_pelanggan";

$pelanggan = $conn->query($query);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Pelanggan</h2>
        <a href="tambah_pelanggan.php" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Pelanggan
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tabelPelanggan">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Pelanggan</th>
                            <th>No. HP</th>
                            <th>Total Transaksi</th>
                            <th>Status Terakhir</th>
                            <th>Tanggal Terakhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = $pelanggan->fetch_assoc()): 
                            $status_class = match($row['status_terakhir'] ?? '') {
                                'Selesai' => 'success',
                                'Diambil' => 'secondary',
                                'Proses' => 'warning',
                                default => 'light'
                            };
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                            <td><?= htmlspecialchars($row['no_hp'] ?? '-') ?></td>
                            <td><?= (int)$row['total_transaksi'] ?> transaksi</td>
                            <td>
                                <?php if ($row['status_terakhir']): ?>
                                    <span class="badge bg-<?= $status_class ?>">
                                        <?= htmlspecialchars($row['status_terakhir']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">Belum ada transaksi</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $row['tanggal_terakhir'] 
                                    ? date('d/m/Y H:i', strtotime($row['tanggal_terakhir'])) 
                                    : '-' 
                                ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="detail_pelanggan.php?id=<?= $row['id_pelanggan'] ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="status_pelanggan.php?id=<?= $row['id_pelanggan'] ?>" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Lihat Status Cucian">
                                        <i class="bi bi-list-check"></i>
                                    </a>
                                    <a href="cetak_nota_pelanggan.php?id=<?= $row['id_pelanggan'] ?>" 
                                       class="btn btn-sm btn-outline-secondary" 
                                       title="Cetak Nota"
                                       target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- DataTables JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#tabelPelanggan').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
        },
        "order": [[1, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": [0, 6] },
            { "searchable": false, "targets": [0, 3, 4, 5, 6] }
        ]
    });
});
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
