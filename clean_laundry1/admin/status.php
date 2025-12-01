<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: /clean_laundry1/login.php'); exit; }
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/header.php';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['id'], $_POST['status'])){
    $id=(int)$_POST['id'];
    $status=trim($_POST['status']);
    $stmt=$conn->prepare('UPDATE transaksi SET status=? WHERE id_transaksi=?');
    $stmt&&$stmt->bind_param('si',$status,$id)&&$stmt->execute();
}
$rows=$conn->query("SELECT t.id_transaksi, p.nama_pelanggan pelanggan, l.nama_layanan layanan, t.berat_cucian, t.status FROM transaksi t JOIN pelanggan p ON p.id_pelanggan=t.id_pelanggan JOIN layanan l ON l.id_layanan=t.id_layanan ORDER BY t.id_transaksi DESC");
?>
<div class="card">
<table class="table table-striped table-hover align-middle">
  <thead><tr><th>Pelanggan</th><th>Layanan</th><th>Berat</th><th>Status</th><th>Aksi</th></tr></thead>
  <tbody>
    <?php if($rows){ while($r=$rows->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($r['pelanggan']) ?></td>
      <td><?= htmlspecialchars($r['layanan']) ?></td>
      <td><?= (float)$r['berat_cucian'] ?></td>
      <td>
        <?php $st=strtolower($r['status']); $cls = ($st==='selesai'?'bg-success':($st==='proses'?'bg-warning text-dark':($st==='diambil'?'bg-primary':'bg-secondary'))); ?>
        <span class="badge <?= $cls ?>"><?= htmlspecialchars($r['status']) ?></span>
      </td>
      <td>
        <form method="post" style="display:inline-flex;gap:6px;align-items:center">
          <input type="hidden" name="id" value="<?= (int)$r['id_transaksi'] ?>" />
          <select name="status">
            <?php foreach(['Proses','Selesai','Diambil'] as $s): ?>
              <option <?= $s===$r['status']?'selected':'' ?> value="<?= $s ?>"><?= $s ?></option>
            <?php endforeach; ?>
          </select>
          <button class="button">Update</button>
        </form>
      </td>
    </tr>
    <?php endwhile; } ?>
  </tbody>
</table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
