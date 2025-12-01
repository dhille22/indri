<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header('Location: /clean_laundry1/login.php');
  exit;
}
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/header.php';
// simple message holder for UI feedback
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['create'])) {
    $nama = trim($_POST['nama'] ?? '');
    $harga = (float) ($_POST['harga'] ?? 0);
    $stmt = $conn->prepare('INSERT INTO layanan(nama_layanan, harga_perkg) VALUES(?,?)');
    $stmt && $stmt->bind_param('sd', $nama, $harga) && $stmt->execute();
  }
  if (isset($_POST['update'])) {
    $id = (int) $_POST['id'];
    $nama = trim($_POST['nama'] ?? '');
    $harga = (float) ($_POST['harga'] ?? 0);
    $stmt = $conn->prepare('UPDATE layanan SET nama_layanan=?, harga_perkg=? WHERE id_layanan=?');
    $stmt && $stmt->bind_param('sdi', $nama, $harga, $id) && $stmt->execute();
  }
  if (isset($_POST['delete'])) {
    $id = (int) $_POST['id'];
    // check if layanan is referenced in transaksi to avoid FK constraint error
    $check = $conn->prepare('SELECT 1 FROM transaksi WHERE id_layanan=? LIMIT 1');
    if ($check) {
      $check->bind_param('i', $id);
      $check->execute();
      $check->store_result();
      if ($check->num_rows > 0) {
        $error = 'Tidak dapat menghapus: layanan masih dipakai pada transaksi.';
      } else {
        $stmt = $conn->prepare('DELETE FROM layanan WHERE id_layanan=?');
        if ($stmt) {
          $stmt->bind_param('i', $id);
          $stmt->execute();
          $success = 'Layanan berhasil dihapus.';
        }
      }
    }
  }
}
$rows = $conn->query('SELECT * FROM layanan ORDER BY id_layanan DESC');
?>
<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
    <strong> Layanan</strong>
    <a class="btn btn-secondary" href="/clean_laundry1/admin/tambah_layanan.php">Tambah Layanan (Preset)</a>
  </div>
  <?php if ($error): ?>
    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
  <?php elseif ($success): ?>
    <div class="alert alert-success" role="alert"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  
</div>
<div class="card" style="margin-top:16px">
  <table class="table table-striped table-hover align-middle">
    <thead>
      <tr>
        <th>Nama Layanan</th>
        <th>Harga per kg</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rows) {
        while ($r = $rows->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($r['nama_layanan']) ?></td>
            <td>Rp <?= number_format((float) $r['harga_perkg'], 0, ',', '.') ?></td>
            <td>
              <form method="post" class="d-inline-flex gap-2 align-items-center">
                <input type="hidden" name="id" value="<?= (int) $r['id_layanan'] ?>" />
                <input class="form-control" name="nama" value="<?= htmlspecialchars($r['nama_layanan']) ?>" />
                <input class="form-control" name="harga" type="number" step="0.01"
                  value="<?= (float) $r['harga_perkg'] ?>" />
                <button name="update" class="btn btn-success">Simpan</button>
                <button name="delete" class="btn btn-outline-danger" onclick="return confirm('Hapus?')">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endwhile; 
      } ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>