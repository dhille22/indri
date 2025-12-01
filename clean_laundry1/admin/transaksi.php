<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: /clean_laundry1/login.php'); exit; }
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/header.php';
if ($_SERVER['REQUEST_METHOD']==='POST'){
    if (isset($_POST['create'])){
        $pelanggan=(int)$_POST['pelanggan'];
        $layanan=(int)$_POST['layanan'];
        $berat=(float)($_POST['berat']??0);
        $status='Proses';
        // ambil harga_perkg untuk hitung total
        $h=0.0; $hr=$conn->query('SELECT harga_perkg FROM layanan WHERE id_layanan='.(int)$layanan); if($hr){ $hv=$hr->fetch_assoc(); $h=(float)$hv['harga_perkg']; }
        $total=$h*$berat;
        $stmt=$conn->prepare('INSERT INTO transaksi(id_pelanggan,id_layanan,tanggal_masuk,berat_cucian,total_biaya,status) VALUES(?,?,?,?,?,?)');
        $tanggal=date('Y-m-d');
        $stmt&&$stmt->bind_param('iisdds',$pelanggan,$layanan,$tanggal,$berat,$total,$status)&&$stmt->execute();
    }
    if (isset($_POST['delete'])){
        $id=(int)$_POST['id'];
        $conn->query('DELETE FROM transaksi WHERE id_transaksi='.(int)$id);
    }
}
$pel=$conn->query('SELECT id_pelanggan, nama_pelanggan FROM pelanggan ORDER BY nama_pelanggan');
$lay=$conn->query('SELECT id_layanan, nama_layanan, harga_perkg FROM layanan ORDER BY nama_layanan');
$sql='SELECT t.id_transaksi, p.nama_pelanggan pelanggan, l.nama_layanan layanan, t.berat_cucian, l.harga_perkg, t.tanggal_masuk, t.status, t.total_biaya FROM transaksi t JOIN pelanggan p ON p.id_pelanggan=t.id_pelanggan JOIN layanan l ON l.id_layanan=t.id_layanan ORDER BY t.id_transaksi DESC';
$rows=$conn->query($sql);
?>
<div class="card">
  <form method="post" class="row g-2">
    <div class="col-md-4">
      <select class="form-select" name="pelanggan" required>
        <option value="">Pilih pelanggan</option>
        <?php if($pel){ while($r=$pel->fetch_assoc()): ?><option value="<?= (int)$r['id_pelanggan'] ?>"><?= htmlspecialchars($r['nama_pelanggan']) ?></option><?php endwhile; } ?>
      </select>
    </div>
    <div class="col-md-4">
      <select class="form-select" name="layanan" required>
        <option value="">Pilih layanan</option>
        <?php if($lay){ while($r=$lay->fetch_assoc()): ?><option value="<?= (int)$r['id_layanan'] ?>"><?= htmlspecialchars($r['nama_layanan']) ?></option><?php endwhile; } ?>
      </select>
    </div>
    <div class="col-md-3">
      <input class="form-control" name="berat" type="number" step="0.01" placeholder="Berat cucian (kg)" required />
    </div>
    <div class="col-md-1 d-grid">
      <button class="btn btn-primary" name="create">Tambah</button>
    </div>
  </form>
</div>
<div class="card mt-3">
  <table class="table table-striped table-hover align-middle">
    <thead><tr><th>Pelanggan</th><th>Layanan</th><th>Berat</th><th>Total</th><th>Tanggal Masuk</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php if($rows){ while($r=$rows->fetch_assoc()): $total=$r['total_biaya']!==null?$r['total_biaya']:$r['harga_perkg']*$r['berat_cucian']; ?>
      <tr>
        <td><?= htmlspecialchars($r['pelanggan']) ?></td>
        <td><?= htmlspecialchars($r['layanan']) ?></td>
        <td><?= (float)$r['berat_cucian'] ?></td>
        <td>Rp <?= number_format($total,0,',','.') ?></td>
        <td><?= htmlspecialchars($r['tanggal_masuk']) ?></td>
        <td>
          <?php $badge = match($r['status']){ 'Selesai'=>'success','Diambil'=>'secondary', default=>'warning'}; ?>
          <span class="badge text-bg-<?= $badge ?>"><?= htmlspecialchars($r['status']) ?></span>
        </td>
        <td>
          <form method="post" onsubmit="return confirm('Hapus transaksi?')" class="d-inline">
            <input type="hidden" name="id" value="<?= (int)$r['id_transaksi'] ?>" />
            <button name="delete" class="btn btn-outline-danger btn-sm">Hapus</button>
          </form>
        </td>
      </tr>
      <?php endwhile; } ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
