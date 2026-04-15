<?php
require_once '../classes/Auth.php';
Auth::cekLogin();
require_once '../classes/Produk.php';
require_once '../classes/Pelanggan.php';
require_once '../classes/Penjualan.php';

$produkObj    = new Produk();
$pelangganObj = new Pelanggan();
$penjualanObj = new Penjualan();

$produkList    = $produkObj->getAll();
$pelangganList = $pelangganObj->getAll();

$msg = ''; $msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proses'])) {
    $pelangganId = (int)$_POST['pelanggan_id'];
    $items = [];
    if (!empty($_POST['produk_id'])) {
        foreach ($_POST['produk_id'] as $idx => $pid) {
            if ($pid && $_POST['jumlah'][$idx] > 0) {
                $items[] = ['produk_id' => (int)$pid, 'jumlah' => (int)$_POST['jumlah'][$idx]];
            }
        }
    }
    if (empty($items)) {
        $msg = "Pilih minimal satu produk!"; $msgType = 'danger';
    } else {
        $result = $penjualanObj->buat($pelangganId, $_SESSION['user_id'], $items);
        if ($result) {
            header("Location: kasir.php?sukses=" . $result); exit;
        } else {
            $msg = "Gagal menyimpan transaksi. Periksa stok barang."; $msgType = 'danger';
        }
    }
}

$sukses = isset($_GET['sukses']) ? (int)$_GET['sukses'] : null;

include '../includes/header.php';
?>
<div class="topbar"><h1><i class="ph-bold ph-receipt"></i> Transaksi Kasir</h1><span style="font-size:13px;color:var(--muted)"><?= date('d F Y') ?></span></div>
<div class="content">
<?php if ($sukses): ?>
  <div class="alert alert-success"><i class="ph-bold ph-check-circle"></i> Transaksi #<?= $sukses ?> berhasil disimpan!</div>
<?php endif; ?>
<?php if ($msg): ?>
  <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1.4fr 1fr;gap:24px;align-items:start">
  <div class="card">
    <div class="card-header"><h2>Form Transaksi</h2></div>
    <div class="card-body">
    <form method="POST" id="formKasir">
      <div class="form-group">
        <label>Pelanggan</label>
        <select name="pelanggan_id" class="form-control">
          <option value="0">-- Pilih Pelanggan --</option>
          <?php foreach ($pelangganList as $p): ?>
            <option value="<?= $p['PelangganID'] ?>"><?= htmlspecialchars($p['NamaPelanggan']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div id="item-container">
        <div class="item-row form-row cols-3" style="margin-bottom:10px;align-items:end">
          <div class="form-group" style="margin:0">
            <label>Produk</label>
            <select name="produk_id[]" class="form-control produk-select" onchange="updateHarga(this)">
              <option value="">-- Pilih --</option>
              <?php foreach ($produkList as $pr): ?>
                <option value="<?= $pr['ProdukID'] ?>"
                        data-harga="<?= $pr['Harga'] ?>"
                        data-stok="<?= $pr['Stok'] ?>">
                  <?= htmlspecialchars($pr['NamaProduk']) ?> (Stok: <?= $pr['Stok'] ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group" style="margin:0">
            <label>Jumlah</label>
            <input type="number" name="jumlah[]" class="form-control jumlah-input" min="1" value="1" onchange="hitungTotal()">
          </div>
          <div class="form-group" style="margin:0">
            <label>Subtotal</label>
            <input type="text" class="form-control subtotal-display" readonly placeholder="Rp 0" style="background:#f8fafc">
          </div>
        </div>
      </div>

      <button type="button" onclick="tambahBaris()" class="btn btn-secondary btn-sm" style="margin-bottom:20px">
        <i class="ph-bold ph-plus"></i> Tambah Produk
      </button>

      <div style="background:var(--bg);border-radius:8px;padding:16px;margin-bottom:16px">
        <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--muted)">
          <span>Total Belanja</span>
          <strong id="grand-total" style="font-size:20px;color:var(--brand)">Rp 0</strong>
        </div>
      </div>
      <button type="submit" name="proses" class="btn btn-primary" style="width:100%">
        <i class="ph-bold ph-credit-card"></i> Proses Pembayaran
      </button>
    </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2><i class="ph-bold ph-package"></i> Daftar Produk</h2></div>
    <table>
      <thead><tr><th>Nama</th><th>Harga</th><th>Stok</th></tr></thead>
      <tbody>
      <?php foreach ($produkList as $pr): ?>
        <tr>
          <td><?= htmlspecialchars($pr['NamaProduk']) ?></td>
          <td>Rp <?= number_format($pr['Harga'],0,',','.') ?></td>
          <td>
            <span class="badge <?= $pr['Stok']<10?'badge-red':'badge-green' ?>">
              <?= $pr['Stok'] ?>
            </span>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</div>

<script>
const produkData = <?= json_encode(array_column($produkList, null, 'ProdukID')) ?>;

function updateHarga(sel) { hitungTotal(); }

function hitungTotal() {
  let total = 0;
  document.querySelectorAll('.item-row').forEach(row => {
    const sel = row.querySelector('.produk-select');
    const jml = parseInt(row.querySelector('.jumlah-input').value) || 0;
    const sub = row.querySelector('.subtotal-display');
    if (sel.value) {
      const harga = parseFloat(sel.selectedOptions[0].dataset.harga) || 0;
      const subtotal = harga * jml;
      sub.value = 'Rp ' + subtotal.toLocaleString('id-ID');
      total += subtotal;
    } else {
      sub.value = '';
    }
  });
  document.getElementById('grand-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function tambahBaris() {
  const container = document.getElementById('item-container');
  const first = container.querySelector('.item-row');
  const clone = first.cloneNode(true);
  clone.querySelectorAll('input').forEach(i => i.value = i.type==='number'?1:'');
  clone.querySelector('select').selectedIndex = 0;
  const del = document.createElement('button');
  del.type = 'button'; del.innerHTML = '<i class="ph-bold ph-x"></i>';
  del.className = 'btn btn-danger btn-sm';
  del.style.marginTop = '4px';
  del.onclick = () => { clone.remove(); hitungTotal(); };
  clone.appendChild(del);
  container.appendChild(clone);
}
</script>
<?php include '../includes/footer.php'; ?>
