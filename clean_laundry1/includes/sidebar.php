<?php $current = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar list-group list-group-flush py-2">
  <a class="list-group-item list-group-item-action d-flex align-items-center<?= $current==='dashboard.php'?' active':'' ?>" href="/clean_laundry1/admin/dashboard.php">
    <i class="bi bi-house me-2"></i> Beranda
  </a>
  <a class="list-group-item list-group-item-action d-flex align-items-center<?= $current==='pelanggan.php'?' active':'' ?>" href="/clean_laundry1/admin/pelanggan.php">
    <i class="bi bi-people me-2"></i> Pelanggan
  </a>
  <a class="list-group-item list-group-item-action d-flex align-items-center<?= $current==='layanan.php'?' active':'' ?>" href="/clean_laundry1/admin/layanan.php">
    <i class="bi bi-bag-check me-2"></i> Layanan
  </a>
  <a class="list-group-item list-group-item-action d-flex align-items-center<?= $current==='transaksi.php'?' active':'' ?>" href="/clean_laundry1/admin/transaksi.php">
    <i class="bi bi-receipt me-2"></i> Transaksi
  </a>
  <a class="list-group-item list-group-item-action d-flex align-items-center<?= $current==='status.php'?' active':'' ?>" href="/clean_laundry1/admin/status.php">
    <i class="bi bi-clipboard2-check me-2"></i> Status
  </a>
  <a class="list-group-item list-group-item-action d-flex align-items-center<?= $current==='laporan.php'?' active':'' ?>" href="/clean_laundry1/admin/laporan.php">
    <i class="bi bi-graph-up me-2"></i> Laporan
  </a>
</aside>
