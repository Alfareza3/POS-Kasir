<?php
require_once '../classes/Auth.php';
Auth::cekLogin();
require_once '../classes/Produk.php';
$produkObj = new Produk();
$list = $produkObj->getAll();
include '../includes/header.php';
?>
<div class="topbar"><h1><i class="ph-bold ph-package"></i> Stok Barang</h1></div>
<div class="content">
  <div class="card">
    <div class="card-header">
      <h2>Status Stok Semua Produk</h2>
      <span style="font-size:13px;color:var(--muted)"><?= count($list) ?> produk</span>
    </div>
    <table>
      <thead>
        <tr><th>#</th><th>Nama Produk</th><th>Harga</th><th>Stok</th><th>Status</th></tr>
      </thead>
      <tbody>
      <?php foreach ($list as $i => $p): ?>
        <?php
          $status = $p['Stok'] === 0 ? ['Habis','badge-red'] :
                   ($p['Stok'] < 10  ? ['Menipis','badge-amber'] : ['Tersedia','badge-green']);
        ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($p['NamaProduk']) ?></td>
          <td>Rp <?= number_format($p['Harga'],0,',','.') ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:8px">
              <div style="flex:1;background:#e2e8f0;border-radius:99px;height:8px;min-width:80px">
                <div style="background:<?= $p['Stok']<10?'#f59e0b':'#10b981' ?>;
                            width:<?= min(100, $p['Stok']) ?>%;height:8px;border-radius:99px"></div>
              </div>
              <strong><?= $p['Stok'] ?></strong>
            </div>
          </td>
          <td><span class="badge <?= $status[1] ?>"><?= $status[0] ?></span></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
