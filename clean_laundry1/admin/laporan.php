<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: /clean_laundry1/login.php'); exit; }
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/header.php';
$from=$_GET['from']??date('Y-m-01');
$to=$_GET['to']??date('Y-m-d');
$stmt=$conn->prepare("SELECT t.id_transaksi, p.nama_pelanggan pelanggan, l.nama_layanan layanan, t.berat_cucian, l.harga_perkg, t.total_biaya, t.tanggal_masuk, t.status FROM transaksi t JOIN pelanggan p ON p.id_pelanggan=t.id_pelanggan JOIN layanan l ON l.id_layanan=t.id_layanan WHERE DATE(t.tanggal_masuk) BETWEEN ? AND ? ORDER BY t.tanggal_masuk DESC");
$stmt&&$stmt->bind_param('ss',$from,$to)&&$stmt->execute();
$res=$stmt?$stmt->get_result():false;
?>
<div class="card">
  <form method="get" class="grid">
    <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" />
    <input type="date" name="to" value="<?= htmlspecialchars($to) ?>" />
    <button class="button">Tampilkan</button>
    <a class="button" href="/clean_laundry1/admin/cetak_laporan.php?from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>" target="_blank">Cetak</a>
  </form>
</div>
<div class="card" style="margin-top:16px">
  <table class="table table-striped table-hover align-middle">
    <thead><tr><th>Pelanggan</th><th>Layanan</th><th>Berat</th><th>Total</th><th>Tanggal Masuk</th><th>Status</th></tr></thead>
    <tbody>
      <?php if($res){ $grand=0; while($r=$res->fetch_assoc()): $total=$r['total_biaya']!==null?$r['total_biaya']:$r['harga_perkg']*$r['berat_cucian']; $grand+=$total; ?>
      <tr>
        <td><?= htmlspecialchars($r['pelanggan']) ?></td>
        <td><?= htmlspecialchars($r['layanan']) ?></td>
        <td><?= (float)$r['berat_cucian'] ?></td>
        <td>Rp <?= number_format($total,0,',','.') ?></td>
        <td><?= htmlspecialchars($r['tanggal_masuk']) ?></td>
        <td>
          <?php $st=strtolower($r['status']); $cls = ($st==='selesai'?'bg-success':($st==='proses'?'bg-warning text-dark':($st==='batal'?'bg-danger':'bg-secondary'))); ?>
          <span class="badge <?= $cls ?>"><?= htmlspecialchars($r['status']) ?></span>
        </td>
      </tr>
      <?php endwhile; ?>
      <tr><th colspan="3">Grand Total</th><th colspan="3">Rp <?= number_format($grand,0,',','.') ?></th></tr>
      <?php } ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
