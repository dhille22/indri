<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: /clean_laundry1/login.php'); exit; }
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/header.php';
// Hitung metrik ringkas dalam satu query
$countPelanggan = 0;
$countLayanan = 0;
$countTransaksiHariIni = 0;
$q = $conn->query(
  "SELECT 
     (SELECT COUNT(*) FROM pelanggan) AS pelanggan,
     (SELECT COUNT(*) FROM layanan) AS layanan,
     (SELECT COUNT(*) FROM transaksi WHERE DATE(tanggal_masuk)=CURDATE()) AS trx_hari_ini"
);
if ($q) {
  $row = $q->fetch_assoc();
  $countPelanggan = (int) ($row['pelanggan'] ?? 0);
  $countLayanan = (int) ($row['layanan'] ?? 0);
  $countTransaksiHariIni = (int) ($row['trx_hari_ini'] ?? 0);
}
?>
<div class="card mb-3">
  <div class="card-body">
    <h5 class="mb-1">Selamat datang di Clean Laundry</h5>
    <div class="text-muted">Di jamin pakaian anda wangi dan bersih</div>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-md-4">
    <div class="card text-center h-100">
      <div class="card-body">
        <div class="text-muted">Total Pelanggan</div>
        <div class="display-6 fw-semibold"><?= number_format((int)$countPelanggan, 0, ',', '.') ?></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card text-center h-100">
      <div class="card-body">
        <div class="text-muted">Total Layanan</div>
        <div class="display-6 fw-semibold"><?= number_format((int)$countLayanan, 0, ',', '.') ?></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card text-center h-100">
      <div class="card-body">
        <div class="text-muted">Transaksi Hari Ini</div>
        <div class="display-6 fw-semibold"><?= number_format((int)$countTransaksiHariIni, 0, ',', '.') ?></div>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
