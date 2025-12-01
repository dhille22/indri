<?php
session_start();
require __DIR__ . '/config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi';
    } else {
        $stmt = $conn->prepare('SELECT id_admin, username, password FROM admin WHERE username = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if ($row && password_verify($password, $row['password'])) {
                $_SESSION['admin_id'] = (int)$row['id_admin'];
                $_SESSION['admin_username'] = $row['username'];
                header('Location: /clean_laundry1/admin/dashboard.php');
                exit;
            } else {
                $error = 'Login gagal';
            }
        } else {
            $error = 'Kesalahan server';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Login | Clean Laundry</title>
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="/clean_laundry1/assets/css/style.css" />
<style>
  body { background: linear-gradient(135deg, #e6f0ff, #f7f9ff); }
  .brand-title { letter-spacing: .3px; }
  .logo-dot { width:8px; height:8px; border-radius:50%; display:inline-block; background:#0d6efd; margin-left:6px; }
  .shadow-card { box-shadow: 0 10px 25px rgba(0,0,0,.08); }
  .input-wrap { display:block; } /* override old style if exists */
  .alert { border-radius: .5rem; }
  .btn { border-radius: .5rem; }
  .form-control { border-radius: .5rem; }
</style>
</head>
<body>
<div class="min-vh-100 d-flex align-items-center justify-content-center p-3">
  <div class="card shadow-card" style="max-width: 420px; width: 100%">
    <div class="card-body p-4 p-md-5">
      <div class="text-center mb-3">
        <h2 class="brand-title fw-bold mb-0">Clean Laundry<span class="logo-dot"></span></h2>
      </div>
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form method="post" novalidate>
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input class="form-control" type="text" name="username" placeholder="Masukkan username" required />
        </div>
        <div class="mb-2">
          <label class="form-label">Password</label>
          <div class="input-group">
            <input class="form-control" id="password" type="password" name="password" placeholder="Masukkan password" required />
            <button class="btn btn-outline-secondary" type="button" id="togglePwd">Tampil</button>
          </div>
        </div>
        <button class="btn btn-primary w-100 mt-2" type="submit">Masuk</button>
      </form>
      <div class="mt-3">
        <a class="btn btn-outline-primary w-100" href="/clean_laundry1/admin/create_admin.php">Belum punya akun? Buat admin</a>
      </div>
    </div>
  </div>
</div>
<!-- Bootstrap 5 JS -->
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
