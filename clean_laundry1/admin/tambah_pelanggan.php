<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: /clean_laundry1/login.php'); exit; }
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: /clean_laundry1/admin/pelanggan.php'); exit; }

// Ambil data pelanggan
$pel = null;
if ($st = $conn->prepare('SELECT id_pelanggan, nama_pelanggan, no_hp, alamat FROM pelanggan WHERE id_pelanggan=?')){
  $st->bind_param('i', $id);
  if ($st->execute()){
    $res = $st->get_result();
    $pel = $res->fetch_assoc();
  }
  $st->close();
}
if (!$pel) { header('Location: /clean_laundry1/admin/pelanggan.php'); exit; }

// Update submit
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $nama = trim($_POST['nama'] ?? '');
  $telp = trim($_POST['telp'] ?? '');
  $alamat = trim($_POST['alamat'] ?? '');
  if ($stmt = $conn->prepare('UPDATE pelanggan SET nama_pelanggan=?, no_hp=?, alamat=? WHERE id_pelanggan=?')){
    $stmt->bind_param('sssi', $nama, $telp, $alamat, $id);
    $stmt->execute();
    $stmt->close();
  }
  header('Location: /clean_laundry1/admin/pelanggan.php');
  exit;
}
?>
<div class="card">
  <h4 style="margin:0 0 12px 0">Edit Pelanggan</h4>
  <form method="post" class="grid">
    <input class="form-control" name="nama" value="<?= htmlspecialchars($pel['nama_pelanggan']) ?>" placeholder="Nama" required />
    <input class="form-control" name="telp" value="<?= htmlspecialchars($pel['no_hp']) ?>" placeholder="No HP" />
    <input class="form-control" name="alamat" value="<?= htmlspecialchars($pel['alamat']) ?>" placeholder="Alamat" />
    <div style="align-self:end">
      <button class="btn btn-primary">Simpan</button>
      <a class="button" href="/clean_laundry1/admin/pelanggan.php">Batal</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
