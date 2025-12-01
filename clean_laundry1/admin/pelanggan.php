<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: /clean_laundry1/login.php'); exit; }
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/header.php';
$alert = '';
if ($_SERVER['REQUEST_METHOD']==='POST'){
    if (isset($_POST['create'])){
        $nama=trim($_POST['nama']??'');$telp=trim($_POST['telp']??'');$alamat=trim($_POST['alamat']??'');
        $stmt=$conn->prepare('INSERT INTO pelanggan(nama_pelanggan,no_hp,alamat) VALUES(?,?,?)');
        $stmt&&$stmt->bind_param('sss',$nama,$telp,$alamat)&&$stmt->execute();
    }
    if (isset($_POST['update'])){
        $id=(int)$_POST['id'];$nama=trim($_POST['nama']??'');$telp=trim($_POST['telp']??'');$alamat=trim($_POST['alamat']??'');
        $stmt=$conn->prepare('UPDATE pelanggan SET nama_pelanggan=?, no_hp=?, alamat=? WHERE id_pelanggan=?');
        $stmt&&$stmt->bind_param('sssi',$nama,$telp,$alamat,$id)&&$stmt->execute();
    }
    // Hapus dinonaktifkan: gunakan edit saja
}
$rows=$conn->query('SELECT * FROM pelanggan ORDER BY id_pelanggan DESC');
?>
<div class="card">
  <form method="post" class="grid">
    <input class="form-control" name="nama" placeholder="Nama" required />
    <input class="form-control" name="telp" placeholder="No HP" />
    <input class="form-control" name="alamat" placeholder="Alamat" />
    <button class="btn btn-primary" name="create">Tambah</button>
  </form>
</div>
<?php if ($alert): ?>
<div class="card" style="margin-top:16px;background:#fff3cd;border:1px solid #ffeeba;color:#856404;padding:10px">
  <?= htmlspecialchars($alert) ?>
</div>
<?php endif; ?>
<div class="card" style="margin-top:16px">
  <table class="table table-striped table-hover align-middle">
    <thead><tr><th>Nama</th><th>No HP</th><th>Alamat</th><th></th></tr></thead>
    <tbody>
      <?php if($rows){ while($r=$rows->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($r['nama_pelanggan']) ?></td>
        <td><?= htmlspecialchars($r['no_hp']) ?></td>
        <td><?= htmlspecialchars($r['alamat']) ?></td>
        <td>
          <form method="post" class="d-inline-flex gap-2 align-items-center">
            <input type="hidden" name="id" value="<?= (int)$r['id_pelanggan'] ?>" />
            <input class="form-control" name="nama" value="<?= htmlspecialchars($r['nama_pelanggan']) ?>" />
            <input class="form-control" name="telp" value="<?= htmlspecialchars($r['no_hp']) ?>" />
            <input class="form-control" name="alamat" value="<?= htmlspecialchars($r['alamat']) ?>" />
            <button name="update" class="btn btn-success">Simpan</button>
          </form>
        </td>
      </tr>
      <?php endwhile; } ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
