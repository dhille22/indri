<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: /clean_laundry1/login.php'); exit; }
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/header.php';

// Handle submit
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $nama = trim($_POST['nama_layanan'] ?? '');
  $harga = (float)($_POST['harga_perkg'] ?? 0);
  if ($nama !== '' && $harga > 0) {
    if ($stmt = $conn->prepare('INSERT INTO layanan(nama_layanan, harga_perkg) VALUES(?, ?)')) {
      $stmt->bind_param('sd', $nama, $harga);
      $stmt->execute();
      $stmt->close();
    }
    header('Location: /clean_laundry1/admin/layanan.php');
    exit;
  }
}
?>
<div class="card">
  <h4 style="margin:0 0 12px 0">Tambah Layanan (Preset seperti di banner)</h4>
  <form method="post" class="grid" id="formPreset">
    <div>
      <label>Kategori</label>
      <select id="kategori" class="form-control">
        <option value="regular">Regular Service (per Kg)</option>
        <option value="oneday">One Day Service (per Kg)</option>
        <option value="express">Express Service (per Kg)</option>
        <option value="satuan">List Harga Satuan</option>
      </select>
    </div>
    <div>
      <label>Item</label>
      <select id="item" class="form-control"></select>
    </div>
    <div>
      <label>Nama Layanan</label>
      <input class="form-control" id="nama_layanan" name="nama_layanan" placeholder="Nama layanan" required />
    </div>
    <div>
      <label>Harga</label>
      <input class="form-control" id="harga_perkg" name="harga_perkg" type="number" step="1" placeholder="Masukkan harga" required />
    </div>
    <div style="align-self:end">
      <button class="btn btn-primary">Simpan</button>
      <a class="button" href="/clean_laundry1/admin/layanan.php">Batal</a>
    </div>
  </form>
</div>

<script>
// Preset item dan harga berdasarkan daftar yang diberikan
const PRESETS = {
  regular: [
    'Cuci Setrika (3 Hari)',
    'Cuci Lipat (3 Hari)',
    'Setrika (3 Hari)'
  ],
  oneday: [
    'Cuci Setrika (1 Hari)',
    'Cuci Lipat (1 Hari)',
    'Setrika (1 Hari)'
  ],
  express: [
    'Cuci Setrika (3 Jam)',
    'Cuci Lipat (3 Jam)',
    'Setrika (3 Jam)',
    'Cuci Setrika (8 Jam)',
    'Cuci Lipat (8 Jam)',
    'Setrika (8 Jam)'
  ],
  satuan: [
    'Selimut Kecil (3 Hari)',
    'Selimut Sedang (3 Hari)',
    'Selimut Besar (3 Hari)',
    'Seprei Kecil Biasa (3 Hari)',
    'Seprei Sedang Biasa (3 Hari)',
    'Seprei Besar Biasa (3 Hari)',
    'Seprei Kecil Premium (3 Hari)',
    'Seprei Sedang Premium (3 Hari)',
    'Seprei Besar Premium (3 Hari)',
    'Bedcover (3 Hari)',
    'Bedcover 1 Set (3 Hari)'
  ]
};

const PRICES = {
  // Regular Service per Kg
  'Cuci Setrika (3 Hari)': 6000,
  'Cuci Lipat (3 Hari)': 4000,
  'Setrika (3 Hari)': 4000,

  // One Day Service per Kg
  'Cuci Setrika (1 Hari)': 8000,
  'Cuci Lipat (1 Hari)': 6000,
  'Setrika (1 Hari)': 6000,

  // Express Service per Kg
  'Cuci Setrika (3 Jam)': 15000,
  'Cuci Lipat (3 Jam)': 12000,
  'Setrika (3 Jam)': 12000,
  'Cuci Setrika (8 Jam)': 12000,
  'Cuci Lipat (8 Jam)': 10000,
  'Setrika (8 Jam)': 10000,

  // List Harga Satuan
  'Selimut Kecil (3 Hari)': 10000,
  'Selimut Sedang (3 Hari)': 15000,
  'Selimut Besar (3 Hari)': 20000,
  'Seprei Kecil Biasa (3 Hari)': 10000,
  'Seprei Sedang Biasa (3 Hari)': 12000,
  'Seprei Besar Biasa (3 Hari)': 15000,
  'Seprei Kecil Premium (3 Hari)': 12000,
  'Seprei Sedang Premium (3 Hari)': 15000,
  'Seprei Besar Premium (3 Hari)': 18000,
  'Bedcover (3 Hari)': 30000,
  'Bedcover 1 Set (3 Hari)': 35000
};

const kategoriEl = document.getElementById('kategori');
const itemEl = document.getElementById('item');
const namaEl = document.getElementById('nama_layanan');
const hargaEl = document.getElementById('harga_perkg');

function renderItems() {
  const cat = kategoriEl.value;
  const items = PRESETS[cat] || [];
  itemEl.innerHTML = '';
  items.forEach((txt, idx) => {
    const opt = document.createElement('option');
    opt.value = txt;
    opt.textContent = txt;
    if (idx === 0) opt.selected = true;
    itemEl.appendChild(opt);
  });
  syncName();
  syncPrice();
}

function syncName() {
  const cat = kategoriEl.value;
  const selected = itemEl.value || '';
  let prefix = '';
  if (cat === 'regular') prefix = '[Regular] ';
  if (cat === 'oneday') prefix = '[One Day] ';
  if (cat === 'express') prefix = '[Express] ';
  if (cat === 'satuan') prefix = '[Satuan] ';
  namaEl.value = prefix + selected;
}

function syncPrice() {
  const selected = itemEl.value || '';
  const price = PRICES[selected];
  hargaEl.value = (price !== undefined) ? price : '';
}

kategoriEl.addEventListener('change', renderItems);
itemEl.addEventListener('change', function(){ syncName(); syncPrice(); });

renderItems();
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
