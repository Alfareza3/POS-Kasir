<?php
require_once '../classes/Auth.php';
Auth::cekAdmin();
require_once '../classes/Produk.php';
$produkObj = new Produk();

$msg = ''; $msgType = 'success';
$edit = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama']);
    $harga = (float)$_POST['harga'];
    $stok  = (int)$_POST['stok'];

    if (isset($_POST['simpan'])) {
        $ok = $produkObj->tambah($nama, $harga, $stok);
        $msg = $ok ? "Produk berhasil ditambahkan!" : "Gagal menambahkan produk.";
        $msgType = $ok ? 'success' : 'danger';
    } elseif (isset($_POST['update'])) {
        $ok = $produkObj->edit((int)$_POST['id'], $nama, $harga, $stok);
        $msg = $ok ? "Produk berhasil diupdate!" : "Gagal mengupdate produk.";
        $msgType = $ok ? 'success' : 'danger';
    }
}

if (isset($_GET['hapus'])) {
    $ok = $produkObj->hapus((int)$_GET['hapus']);
    $msg = $ok ? "Produk dihapus." : "Gagal menghapus produk.";
    $msgType = $ok ? 'success' : 'danger';
}

if (isset($_GET['edit'])) {
    $edit = $produkObj->getById((int)$_GET['edit']);
}

$list = $produkObj->getAll();
include '../includes/header.php';
?>
<div class="topbar"><h1><i class="ph-bold ph-tag"></i> Pendataan Barang</h1></div>
<div class="content">
<?php if ($msg): ?>
  <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:24px;align-items:start">
  <div class="card">
    <div class="card-header"><h2><?= $edit ? '<i class="ph-bold ph-pencil-simple"></i> Edit Produk' : '<i class="ph-bold ph-plus"></i> Tambah Produk' ?></h2></div>
    <div class="card-body">
    <form method="POST">
      <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['ProdukID'] ?>"><?php endif; ?>
      <div class="form-group">
        <label>Nama Produk</label>
        <input type="text" name="nama" class="form-control" required
               value="<?= htmlspecialchars($edit['NamaProduk'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Harga (Rp)</label>
        <input type="number" name="harga" class="form-control" min="0" step="100" required
               value="<?= $edit['Harga'] ?? '' ?>">
      </div>
      <div class="form-group">
        <label>Stok</label>
        <input type="number" name="stok" class="form-control" min="0" required
               value="<?= $edit['Stok'] ?? '' ?>">
      </div>
      <?php if ($edit): ?>
        <button name="update" class="btn btn-primary" style="width:100%"><i class="ph-bold ph-floppy-disk"></i> Update</button>
        <a href="produk.php" class="btn btn-secondary" style="width:100%;margin-top:8px;text-align:center">Batal</a>
      <?php else: ?>
        <button name="simpan" class="btn btn-primary" style="width:100%"><i class="ph-bold ph-plus"></i> Tambah</button>
      <?php endif; ?>
    </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Daftar Produk</h2>
      <span style="font-size:13px;color:var(--muted)"><?= count($list) ?> produk</span>
    </div>
    <table>
      <thead><tr><th>#</th><th>Nama Produk</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($list as $i => $p): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($p['NamaProduk']) ?></td>
          <td>Rp <?= number_format($p['Harga'],0,',','.') ?></td>
          <td><span class="badge <?= $p['Stok']<10?'badge-red':'badge-green' ?>"><?= $p['Stok'] ?></span></td>
          <td>
            <a href="?edit=<?= $p['ProdukID'] ?>" class="btn btn-secondary btn-sm"><i class="ph-bold ph-pencil-simple"></i></a>
            <a href="?hapus=<?= $p['ProdukID'] ?>" class="btn btn-danger btn-sm"
               onclick="return confirm('Hapus produk ini?')"><i class="ph-bold ph-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<?php include '../includes/footer.php'; ?>
