<?php
require_once '../classes/Auth.php';
Auth::cekAdmin();
require_once '../classes/Pelanggan.php';
$pelangganObj = new Pelanggan();

$msg = ''; $msgType = 'success';
$edit = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = trim($_POST['nama']);
    $alamat  = trim($_POST['alamat']);
    $telepon = trim($_POST['telepon']);

    if (isset($_POST['simpan'])) {
        $ok = $pelangganObj->tambah($nama, $alamat, $telepon);
        $msg = $ok ? "Pelanggan berhasil ditambahkan!" : "Gagal menambahkan pelanggan.";
        $msgType = $ok ? 'success' : 'danger';
    } elseif (isset($_POST['update'])) {
        $ok = $pelangganObj->edit((int)$_POST['id'], $nama, $alamat, $telepon);
        $msg = $ok ? "Data pelanggan diperbarui!" : "Gagal memperbarui data.";
        $msgType = $ok ? 'success' : 'danger';
    }
}

if (isset($_GET['hapus'])) {
    $ok = $pelangganObj->hapus((int)$_GET['hapus']);
    $msg = $ok ? "Pelanggan dihapus." : "Gagal menghapus pelanggan.";
    $msgType = $ok ? 'success' : 'danger';
}

if (isset($_GET['edit'])) {
    $edit = $pelangganObj->getById((int)$_GET['edit']);
}

$list = $pelangganObj->getAll();
include '../includes/header.php';
?>
<div class="topbar"><h1><i class="ph-bold ph-users"></i> Data Pelanggan</h1><span style="font-size:13px;color:var(--muted)"><?= date('d F Y') ?></span></div>
<div class="content">
<?php if ($msg): ?>
  <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:24px;align-items:start">
  <div class="card">
    <div class="card-header"><h2><?= $edit ? '<i class="ph-bold ph-pencil-simple"></i> Edit Pelanggan' : '<i class="ph-bold ph-plus"></i> Tambah Pelanggan' ?></h2></div>
    <div class="card-body">
    <form method="POST">
      <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['PelangganID'] ?>"><?php endif; ?>
      <div class="form-group">
        <label>Nama Pelanggan</label>
        <input type="text" name="nama" class="form-control" required
               value="<?= htmlspecialchars($edit['NamaPelanggan'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Alamat</label>
        <textarea name="alamat" class="form-control" rows="3"
                  style="resize:vertical"><?= htmlspecialchars($edit['Alamat'] ?? '') ?></textarea>
      </div>
      <div class="form-group">
        <label>Nomor Telepon</label>
        <input type="text" name="telepon" class="form-control"
               value="<?= htmlspecialchars($edit['NomorTelepon'] ?? '') ?>">
      </div>
      <?php if ($edit): ?>
        <button name="update" class="btn btn-primary" style="width:100%"><i class="ph-bold ph-floppy-disk"></i> Update</button>
        <a href="pelanggan.php" class="btn btn-secondary" style="width:100%;margin-top:8px;text-align:center;display:block">Batal</a>
      <?php else: ?>
        <button name="simpan" class="btn btn-primary" style="width:100%"><i class="ph-bold ph-plus"></i> Tambah</button>
      <?php endif; ?>
    </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h2>Daftar Pelanggan</h2>
      <span style="font-size:13px;color:var(--muted)"><?= count($list) ?> pelanggan</span>
    </div>
    <table>
      <thead>
        <tr><th>#</th><th>Nama</th><th>Telepon</th><th>Alamat</th><th>Aksi</th></tr>
      </thead>
      <tbody>
      <?php if (empty($list)): ?>
        <tr><td colspan="5" style="text-align:center;padding:24px;color:var(--muted)">Belum ada data pelanggan.</td></tr>
      <?php else: ?>
        <?php foreach ($list as $i => $p): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><strong><?= htmlspecialchars($p['NamaPelanggan']) ?></strong></td>
            <td><?= htmlspecialchars($p['NomorTelepon'] ?: '-') ?></td>
            <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
              <?= htmlspecialchars($p['Alamat'] ?: '-') ?>
            </td>
            <td>
              <a href="?edit=<?= $p['PelangganID'] ?>" class="btn btn-secondary btn-sm"><i class="ph-bold ph-pencil-simple"></i></a>
              <a href="?hapus=<?= $p['PelangganID'] ?>" class="btn btn-danger btn-sm"
                 onclick="return confirm('Hapus pelanggan ini?')"><i class="ph-bold ph-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<?php include '../includes/footer.php'; ?>
