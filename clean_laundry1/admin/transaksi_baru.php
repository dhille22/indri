<?php
session_start();
if (!isset($_SESSION['admin_id'])) { 
    header('Location: /clean_laundry1/login.php'); 
    exit; 
}

require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/header.php';

// Inisialisasi pesan error
$error = '';
$success = '';

// Proses form jika ada data yang dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_transaksi'])) {
        // Validasi input
        $pelanggan_id = (int)$_POST['pelanggan_id'];
        $tanggal_masuk = $_POST['tanggal_masuk'];
        $layanan = $_POST['layanan'] ?? [];
        $berat = $_POST['berat'] ?? [];
        $keterangan = $_POST['keterangan'] ?? '';
        
        // Validasi data
        if (empty($pelanggan_id)) {
            $error = 'Pilih pelanggan terlebih dahulu';
        } elseif (empty($layanan) || !is_array($layanan) || count($layanan) === 0) {
            $error = 'Pilih minimal satu layanan';
        } else {
            // Mulai transaksi database
            $conn->begin_transaction();
            
            try {
                // 1. Buat transaksi baru
                $stmt = $conn->prepare("INSERT INTO transaksi (id_pelanggan, tanggal_masuk, keterangan, status) VALUES (?, ?, ?, 'Diterima')");
                $stmt->bind_param('iss', $pelanggan_id, $tanggal_masuk, $keterangan);
                $stmt->execute();
                
                $transaksi_id = $conn->insert_id;
                $total_biaya = 0;
                
                // 2. Simpan detail layanan
                $stmt_detail = $conn->prepare("INSERT INTO transaksi_detail (id_transaksi, id_layanan, berat, subtotal) VALUES (?, ?, ?, ?)");
                
                foreach ($layanan as $index => $id_layanan) {
                    if (!empty($berat[$index]) && $berat[$index] > 0) {
                        // Dapatkan harga layanan
                        $layanan_result = $conn->query("SELECT harga_perkg FROM layanan WHERE id_layanan = " . (int)$id_layanan);
                        if ($layanan_row = $layanan_result->fetch_assoc()) {
                            $harga = $layanan_row['harga_perkg'];
                            $subtotal = $harga * (float)$berat[$index];
                            $total_biaya += $subtotal;
                            
                            // Simpan detail transaksi
                            $stmt_detail->bind_param('iidd', $transaksi_id, $id_layanan, $berat[$index], $subtotal);
                            $stmt_detail->execute();
                        }
                    }
                }
                
                // 3. Update total biaya di transaksi
                $conn->query("UPDATE transaksi SET total_biaya = $total_biaya WHERE id_transaksi = $transaksi_id");
                
                // Commit transaksi
                $conn->commit();
                
                $success = "Transaksi berhasil disimpan dengan ID: $transaksi_id";
                
                // Redirect ke halaman cetak nota
                header("Location: cetak_nota.php?id=$transaksi_id");
                exit;
                
            } catch (Exception $e) {
                // Rollback transaksi jika terjadi error
                $conn->rollback();
                $error = "Terjadi kesalahan: " . $e->getMessage();
            }
        }
    }
}

// Ambil data pelanggan dan layanan
$pelanggan = $conn->query("SELECT id_pelanggan, nama_pelanggan, no_hp FROM pelanggan ORDER BY nama_pelanggan");
$layanan = $conn->query("SELECT * FROM layanan WHERE status = 'Aktif' ORDER BY nama_layanan");
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">Transaksi Baru</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <form method="post" id="transaksiForm">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Informasi Pelanggan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilih Pelanggan</label>
                                    <select class="form-select" name="pelanggan_id" required>
                                        <option value="">-- Pilih Pelanggan --</option>
                                        <?php while ($row = $pelanggan->fetch_assoc()): ?>
                                            <option value="<?= $row['id_pelanggan'] ?>">
                                                <?= htmlspecialchars($row['nama_pelanggan'] . ' (' . $row['no_hp'] . ')') ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Masuk</label>
                                    <input type="datetime-local" class="form-control" name="tanggal_masuk" 
                                           value="<?= date('Y-m-d\TH:i') ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan (Opsional)</label>
                            <textarea class="form-control" name="keterangan" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detail Layanan</h5>
                        <button type="button" class="btn btn-sm btn-light" id="tambahLayanan">
                            <i class="bi bi-plus-circle"></i> Tambah Layanan
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="layananContainer">
                            <!-- Baris layanan akan ditambahkan di sini oleh JavaScript -->
                            <div class="row layanan-row mb-3">
                                <div class="col-md-5">
                                    <select class="form-select layanan-select" name="layanan[]" required>
                                        <option value="">-- Pilih Layanan --</option>
                                        <?php while ($row = $layanan->fetch_assoc()): ?>
                                            <option value="<?= $row['id_layanan'] ?>" data-harga="<?= $row['harga_perkg'] ?>">
                                                <?= htmlspecialchars($row['nama_layanan'] . ' (Rp ' . number_format($row['harga_perkg'], 0, ',', '.') . '/kg)') ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="number" class="form-control berat" name="berat[]" step="0.1" min="0.1" placeholder="Berat (kg)" required>
                                        <span class="input-group-text">kg</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control subtotal" readonly>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-sm hapus-layanan" disabled>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>Total:</strong>
                                    <span id="totalHarga">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                    <a href="transaksi.php" class="btn btn-secondary me-md-2">Kembali</a>
                    <button type="submit" name="create_transaksi" class="btn btn-primary">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Template untuk baris layanan baru -->
<template id="layananTemplate">
    <div class="row layanan-row mb-3">
        <div class="col-md-5">
            <select class="form-select layanan-select" name="layanan[]" required>
                <option value="">-- Pilih Layanan --</option>
                <?php 
                $layanan->data_seek(0); // Reset pointer ke awal
                while ($row = $layanan->fetch_assoc()): ?>
                    <option value="<?= $row['id_layanan'] ?>" data-harga="<?= $row['harga_perkg'] ?>">
                        <?= htmlspecialchars($row['nama_layanan'] . ' (Rp ' . number_format($row['harga_perkg'], 0, ',', '.') . '/kg)') ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <input type="number" class="form-control berat" name="berat[]" step="0.1" min="0.1" placeholder="Berat (kg)" required>
                <span class="input-group-text">kg</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control subtotal" readonly>
            </div>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-sm hapus-layanan">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('layananContainer');
    const addButton = document.getElementById('tambahLayanan');
    const form = document.getElementById('transaksiForm');
    const template = document.getElementById('layananTemplate');
    
    // Tambah baris layanan
    function addLayananRow() {
        const newRow = template.content.cloneNode(true);
        container.appendChild(newRow);
        updateHapusButtons();
    }
    
    // Update tombol hapus
    function updateHapusButtons() {
        const rows = container.querySelectorAll('.layanan-row');
        const deleteButtons = container.querySelectorAll('.hapus-layanan');
        
        deleteButtons.forEach((btn, index) => {
            // Hapus event listener yang ada
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
            
            // Tambah event listener baru
            newBtn.addEventListener('click', function() {
                if (rows.length > 1) {
                    this.closest('.layanan-row').remove();
                    updateHapusButtons();
                    hitungTotal();
                }
            });
            
            // Nonaktifkan tombol hapus jika hanya tersisa satu baris
            newBtn.disabled = rows.length <= 1;
        });
    }
    
    // Hitung subtotal dan total
    function hitungSubtotal(input) {
        const row = input.closest('.layanan-row');
        const select = row.querySelector('.layanan-select');
        const beratInput = row.querySelector('.berat');
        const subtotalInput = row.querySelector('.subtotal');
        
        if (select.value && beratInput.value) {
            const harga = parseFloat(select.selectedOptions[0].dataset.harga);
            const berat = parseFloat(beratInput.value);
            const subtotal = harga * berat;
            subtotalInput.value = subtotal.toLocaleString('id-ID');
        } else {
            subtotalInput.value = '';
        }
        
        hitungTotal();
    }
    
    // Hitung total semua layanan
    function hitungTotal() {
        let total = 0;
        document.querySelectorAll('.layanan-row').forEach(row => {
            const subtotal = row.querySelector('.subtotal').value;
            if (subtotal) {
                total += parseFloat(subtotal.replace(/\./g, '').replace(',', '.'));
            }
        });
        
        document.getElementById('totalHarga').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }
    
    // Event listeners
    addButton.addEventListener('click', addLayananRow);
    
    container.addEventListener('change', function(e) {
        if (e.target.classList.contains('layanan-select') || e.target.classList.contains('berat')) {
            hitungSubtotal(e.target);
        }
    });
    
    container.addEventListener('input', function(e) {
        if (e.target.classList.contains('berat')) {
            hitungSubtotal(e.target);
        }
    });
    
    // Inisialisasi
    updateHapusButtons();
    
    // Validasi form sebelum submit
    form.addEventListener('submit', function(e) {
        let valid = true;
        const rows = container.querySelectorAll('.layanan-row');
        
        rows.forEach(row => {
            const select = row.querySelector('.layanan-select');
            const berat = row.querySelector('.berat');
            
            if (!select.value || !berat.value || parseFloat(berat.value) <= 0) {
                valid = false;
                if (!select.value) select.classList.add('is-invalid');
                if (!berat.value || parseFloat(berat.value) <= 0) berat.classList.add('is-invalid');
            } else {
                select.classList.remove('is-invalid');
                berat.classList.remove('is-invalid');
            }
        });
        
        if (!valid) {
            e.preventDefault();
            alert('Harap isi semua layanan dengan benar');
        }
    });
});
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
