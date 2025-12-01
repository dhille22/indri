<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header('Location: /clean_laundry1/admin/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clean Laundry - Layanan Laundry Terpercaya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .hero-section {
            background: linear-gradient(rgba(13, 110, 253, 0.9), rgba(13, 110, 253, 0.8)), 
                        url('https://source.unsplash.com/random/1200x800/?laundry') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #0d6efd;
            margin-bottom: 1rem;
        }
        .feature-card {
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            height: 100%;
            transition: transform 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .status-box {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/clean_laundry1">Clean Laundry</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#layanan">Layanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kontak">Kontak</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a href="status_cucian.php" class="btn btn-outline-light">
                            <i class="bi bi-search"></i> Cek Status Cucian
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a href="login.php" class="btn btn-light">
                            <i class="bi bi-box-arrow-in-right"></i> Login Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Layanan Laundry Berkualitas Tinggi</h1>
            <p class="lead mb-5">Cucian bersih, wangi, dan rapi hanya dengan satu kali klik</p>
            
            <!-- Status Box -->
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="status-box">
                            <h4 class="mb-4">Cek Status Cucian Anda</h4>
                            <form action="status_cucian.php" method="post" class="row g-3">
                                <div class="col-md-5">
                                    <input type="text" class="form-control form-control-lg" name="no_hp" 
                                           placeholder="Nomor HP" required>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" class="form-control form-control-lg" name="kode_transaksi" 
                                           placeholder="Kode Transaksi" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="bi bi-search"></i> Cek
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan Section -->
    <section id="layanan" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2>Layanan Kami</h2>
                <p class="text-muted">Berbagai pilihan layanan laundry untuk kebutuhan Anda</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-tsunami"></i>
                        </div>
                        <h4>Cuci Biasa</h4>
                        <p class="text-muted">Layanan cuci biasa dengan detergen berkualitas tinggi untuk pakaian sehari-hari.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-stars"></i>
                        </div>
                        <h4>Cuci Kilat</h4>
                        <p class="text-muted">Butuh cepat? Layanan cuci kilat kami selesai dalam waktu 6 jam.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-umbrella"></i>
                        </div>
                        <h4>Dry Cleaning</h4>
                        <p class="text-muted">Perawatan khusus untuk pakaian berbahan khusus seperti jas dan gaun.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang Kami -->
    <section id="tentang" class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2>Tentang Kami</h2>
                    <p class="lead">Clean Laundry hadir untuk memberikan solusi praktis bagi kebutuhan laundry Anda.</p>
                    <p>Dengan pengalaman lebih dari 5 tahun, kami berkomitmen memberikan pelayanan terbaik dengan hasil yang memuaskan. Setiap pakaian ditangani dengan penuh perhatian dan menggunakan peralatan modern.</p>
                    <div class="d-flex align-items-center mt-4">
                        <div class="me-4">
                            <h3 class="text-primary mb-0">5+</h3>
                            <p class="text-muted mb-0">Tahun Pengalaman</p>
                        </div>
                        <div class="me-4">
                            <h3 class="text-primary mb-0">1000+</h3>
                            <p class="text-muted mb-0">Pelanggan Setia</p>
                        </div>
                        <div>
                            <h3 class="text-primary mb-0">99%</h3>
                            <p class="text-muted mb-0">Kepuasan Pelanggan</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0">
                    <img src="https://source.unsplash.com/random/600x400/?laundry-service" alt="Tentang Kami" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Kontak -->
    <section id="kontak" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2>Hubungi Kami</h2>
                <p class="text-muted">Kami siap membantu kebutuhan laundry Anda</p>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h4>Lokasi</h4>
                        <p class="text-muted">Jl. Contoh No. 123<br>Kota Anda, 12345</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <h4>Telepon</h4>
                        <p class="text-muted">(021) 12345678<br>Senin - Minggu, 08:00 - 20:00 WIB</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <h4>Email</h4>
                        <p class="text-muted">info@cleanlaundry.com<br>cs@cleanlaundry.com</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Clean Laundry</h5>
                    <p class="text-muted">Solusi praktis untuk kebutuhan laundry Anda.</p>
                </div>
                <div class="col-md-3">
                    <h5>Link Cepat</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-decoration-none text-muted">Beranda</a></li>
                        <li><a href="#layanan" class="text-decoration-none text-muted">Layanan</a></li>
                        <li><a href="#tentang" class="text-decoration-none text-muted">Tentang Kami</a></li>
                        <li><a href="#kontak" class="text-decoration-none text-muted">Kontak</a></li>
                        <li><a href="status_cucian.php" class="text-decoration-none text-muted">Cek Status Cucian</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Ikuti Kami</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white"><i class="bi bi-facebook fs-4"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-instagram fs-4"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-whatsapp fs-4"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center text-muted">
                <p class="mb-0">&copy; <?= date('Y') ?> Clean Laundry. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
