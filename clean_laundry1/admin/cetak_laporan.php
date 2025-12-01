<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: /clean_laundry1/login.php'); exit; }
require __DIR__ . '/../config/database.php';
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');
$mode = $_GET['mode'] ?? 'ttd'; // 'ttd' (signature) or 'qr'
header('Content-Type: text/html; charset=utf-8');

// Ambil info admin sederhana (username) untuk ditampilkan di tanda tangan / barcode
$adminName = 'Admin';
if (isset($_SESSION['admin_id'])) {
  if ($stmtAdmin = $conn->prepare('SELECT username FROM admin WHERE id_admin = ?')) {
    $stmtAdmin->bind_param('i', $_SESSION['admin_id']);
    if ($stmtAdmin->execute()) {
      $resAdmin = $stmtAdmin->get_result();
      if ($rowA = $resAdmin->fetch_assoc()) {
        $adminName = $rowA['username'] ?: $adminName;
      }
    }
    $stmtAdmin->close();
  }
}
?><!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><title>Cetak Laporan</title>
<style>
 body{font-family:Arial,sans-serif}
 table{width:100%;border-collapse:collapse}
 th,td{border:1px solid #999;padding:6px}
 thead th{background:#f8f9fa}
 tbody tr:nth-child(even){background:#f6f6f6}
 .badge{display:inline-block;padding:.35em .6em;font-size:.75rem;border-radius:.35rem;color:#fff}
 .badge-success{background:#198754}
 .badge-warning{background:#ffc107;color:#212529}
 .badge-danger{background:#dc3545}
 .badge-secondary{background:#6c757d}
 .print-meta{margin:12px 0}
 .sign-area{margin-top:28px;display:flex;justify-content:space-between;align-items:flex-start}
 .sign-block{width:320px;text-align:center}
 .sign-line{margin-top:64px;border-top:1px solid #333;padding-top:6px}
 .sign-img{max-width:220px;max-height:90px;display:block;margin:8px auto 4px auto}
 .qr-wrap{width:200px;text-align:center}
 @media print {
   .no-print{display:none !important}
 }
</style>
</head><body onload="window.print()">
<h3>Laporan Transaksi</h3>
<p>Periode: <?= htmlspecialchars($from) ?> s/d <?= htmlspecialchars($to) ?></p>
<table>
<thead><tr><th>Pelanggan</th><th>Layanan</th><th>Berat</th><th>Total</th><th>Tanggal Masuk</th><th>Status</th></tr></thead>
<tbody>
<?php
$stmt=$conn->prepare("SELECT p.nama_pelanggan pelanggan, l.nama_layanan layanan, t.berat_cucian, l.harga_perkg, t.total_biaya, t.tanggal_masuk, t.status FROM transaksi t JOIN pelanggan p ON p.id_pelanggan=t.id_pelanggan JOIN layanan l ON l.id_layanan=t.id_layanan WHERE DATE(t.tanggal_masuk) BETWEEN ? AND ? ORDER BY t.tanggal_masuk DESC");
$stmt&&$stmt->bind_param('ss',$from,$to)&&$stmt->execute();
$res=$stmt?$stmt->get_result():false;
$grand=0;
if($res){ while($r=$res->fetch_assoc()){ $total=$r['total_biaya']!==null?$r['total_biaya']:$r['harga_perkg']*$r['berat_cucian']; $grand+=$total; $st=strtolower($r['status']); $cls=($st==='selesai'?'badge-success':($st==='proses'?'badge-warning':($st==='batal'?'badge-danger':'badge-secondary'))); $statusHtml='<span class="badge '.$cls.'">'.htmlspecialchars($r['status']).'</span>'; echo '<tr><td>'.htmlspecialchars($r['pelanggan']).'</td><td>'.htmlspecialchars($r['layanan']).'</td><td>'.(float)$r['berat_cucian'].'</td><td>Rp '.number_format($total,0,',','.').'</td><td>'.htmlspecialchars($r['tanggal_masuk']).'</td><td>'.$statusHtml."</td></tr>"; }}
?>
<tr><th colspan="3">Grand Total</th><th colspan="3">Rp <?= number_format($grand,0,',','.') ?></th></tr>
</tbody>
</table>
<div class="print-meta">
  <small>Tanggal Cetak: <?= date('Y-m-d H:i') ?></small>
  <span class="no-print" style="float:right">
  </span>
  <div style="clear:both"></div>
  <?php
    $sigPath = __DIR__ . '/../assets/signature.png';
    $sigUrl = file_exists($sigPath) ? '/clean_laundry1/assets/signature.png' : '';
  ?>
</div>

<?php if ($mode === 'qr') { ?>
  <div class="sign-area">
    <div class="qr-wrap">
      <div id="qrcode"></div>
      <div style="margin-top:6px;font-size:12px">Verifikasi Laporan</div>
    </div>
    <div class="sign-block">
      <?php if ($sigUrl) { ?><img class="sign-img" src="<?= $sigUrl ?>" alt="Tanda Tangan"><?php } ?>
      <div class="sign-line">( <?= htmlspecialchars($adminName) ?> )<br><small>Administrator</small></div>
    </div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script>
    (function(){
      var payload = {
        type: 'laporan-transaksi',
        periode: {from: '<?= addslashes($from) ?>', to: '<?= addslashes($to) ?>'},
        grand_total: '<?= number_format($grand,0,',','.') ?>',
        admin: '<?= addslashes($adminName) ?>',
        printed_at: '<?= date('c') ?>'
      };
      new QRCode(document.getElementById('qrcode'), {
        text: JSON.stringify(payload),
        width: 180,
        height: 180
      });
    })();
  </script>
<?php } else { ?>
  <div class="sign-area">
    <div class="sign-block">
      <div style="margin-bottom:8px">Pemilik / Admin</div>
      <?php if ($sigUrl) { ?><img class="sign-img" src="<?= $sigUrl ?>" alt="Tanda Tangan"><?php } else { ?><div style="height:90px"></div><?php } ?>
      <div class="sign-line">( <?= htmlspecialchars($adminName) ?> )</div>
    </div>
  </div>
<?php } ?>
</body></html>
