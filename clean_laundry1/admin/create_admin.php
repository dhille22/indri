<?php
session_start();
require __DIR__ . '/../config/database.php';
$cntRes=$conn->query('SELECT COUNT(*) c FROM admin');
$hasAdmin = ($cntRes && ($row=$cntRes->fetch_assoc()) && (int)$row['c']>0);
$hasAdmin = (bool)$hasAdmin; // tetap gunakan untuk judul, tapi tanpa redirect agar dapat diakses dari login
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $u=trim($_POST['username']??'');
  $p=trim($_POST['password']??'');
  if ($u===''||$p===''){ $error='Username dan password wajib diisi'; }
  else {
    $hash=password_hash($p, PASSWORD_DEFAULT);
    $stmt=$conn->prepare('INSERT INTO admin(username,password) VALUES(?,?)');
    if($stmt){ $stmt->bind_param('ss',$u,$hash); if($stmt->execute()){ header('Location: /clean_laundry1/login.php'); exit; } else { $error='Gagal menyimpan (username mungkin sudah ada)'; } }
    else { $error='Kesalahan server'; }
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo $hasAdmin ? 'Tambah Admin' : 'Buat Admin Pertama'; ?></title>
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="/clean_laundry1/assets/css/style.css" />
<style>
  body { background: linear-gradient(135deg, #e6f0ff, #f7f9ff); }
</style>
</head>
<body>
<div class="min-vh-100 d-flex align-items-center justify-content-center p-3">
  <div class="card shadow-lg" style="max-width: 420px; width: 100%">
    <div class="card-body p-4 p-md-5">
      <h2 class="fw-bold mb-3 text-center"><?php echo $hasAdmin ? 'Tambah Admin' : 'Setup Admin'; ?></h2>
      <?php if($error): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form method="post" novalidate>
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input class="form-control" name="username" placeholder="Masukkan username" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="input-group">
            <input class="form-control" id="password" name="password" type="password" placeholder="Masukkan password" required />
            <button class="btn btn-outline-secondary" type="button" id="togglePwd">Tampil</button>
          </div>
        </div>
        <button class="btn btn-primary w-100" type="submit">Buat Admin</button>
        <div class="text-center mt-3">
          <a href="/clean_laundry1/login.php" class="text-decoration-none">Kembali ke Login</a>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Bootstrap 5 JS (optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
  const btn = document.getElementById('togglePwd');
  const pwd = document.getElementById('password');
  if (btn && pwd) {
    btn.addEventListener('click', () => {
      const isPwd = pwd.type === 'password';
      pwd.type = isPwd ? 'text' : 'password';
      btn.textContent = isPwd ? 'Sembunyikan' : 'Tampil';
    });
  }
</script>
</body>
</html>
