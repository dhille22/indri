<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$logged = isset($_SESSION['admin_id']);
// detect dashboard page to apply special background
$isDashboard = basename($_SERVER['PHP_SELF']) === 'dashboard.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Clean Laundry</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="/clean_laundry1/assets/css/style.css" />
<script src="/clean_laundry1/assets/js/main.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" defer></script>
</head>
<body class="<?= $isDashboard ? 'dashboard-bg' : '' ?>">
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
  <div class="container-fluid px-3">
    <a class="navbar-brand d-flex align-items-center gap-2" href="/clean_laundry1/admin/dashboard.php">
      <img src="/clean_laundry1/assets/img/logo.png" alt="Logo" height="28" />
      <span class="fw-semibold">Clean Laundry</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav" aria-controls="topNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="topNav">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <?php if ($logged): ?>
          <li class="nav-item"><a class="nav-link" href="/clean_laundry1/admin/dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="/clean_laundry1/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/clean_laundry1/login.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
  </nav>
<div class="container-fluid">
  <div class="row">
    <?php if ($logged): ?>
      <div class="col-12 col-md-3 col-lg-2 p-0 border-end bg-light">
        <?php require __DIR__ . '/sidebar.php'; ?>
      </div>
      <div class="col-12 col-md-9 col-lg-10 p-3 p-md-4">
    <?php else: ?>
      <div class="col-12 p-3 p-md-4">
    <?php endif; ?>
<main class="content">
