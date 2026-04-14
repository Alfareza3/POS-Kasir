<?php
require_once '../classes/Auth.php';
Auth::cekLogin();
require_once '../config/database.php';
$db = Database::getInstance()->getConn();

$totalProduk    = $db->query("SELECT COUNT(*) FROM produk")->fetch_row()[0];
$totalPelanggan = $db->query("SELECT COUNT(*) FROM pelanggan")->fetch_row()[0];
$totalPenjualan = $db->query("SELECT COUNT(*) FROM penjualan WHERE TanggalPenjualan = CURDATE()")->fetch_row()[0];
$totalPendapatan= $db->query("SELECT COALESCE(SUM(TotalHarga),0) FROM penjualan WHERE TanggalPenjualan = CURDATE()")->fetch_row()[0];
$stokMenipis    = $db->query("SELECT * FROM produk WHERE Stok < 10 ORDER BY Stok")->fetch_all(MYSQLI_ASSOC);
$transaksiTerbaru = $db->query(
    "SELECT p.*, pl.NamaPelanggan FROM penjualan p
     LEFT JOIN pelanggan pl ON p.PelangganID = pl.PelangganID
     ORDER BY p.PenjualanID DESC LIMIT 5"
)->fetch_all(MYSQLI_ASSOC);

include '../includes/header.php';
?>
<div class="topbar"><h1><i class="ph-bold ph-squares-four"></i> Dashboard</h1><span style="font-size:13px;color:var(--muted)"><?= date('d F Y') ?></span></div>
<div class="content">

  <div class="stat-grid">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="ph-fill ph-receipt"></i></div>
      <div><div class="stat-label">Transaksi Hari Ini</div><div class="stat-value"><?= $totalPenjualan ?></div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon green"><i class="ph-fill ph-currency-circle-dollar"></i></div>
      <div><div class="stat-label">Pendapatan Hari Ini</div><div class="stat-value">Rp <?= number_format($totalPendapatan,0,',','.') ?></div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon amber"><i class="ph-fill ph-package"></i></div>
      <div><div class="stat-label">Total Produk</div><div class="stat-value"><?= $totalProduk ?></div></div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
    <div class="card">
      <div class="card-header"><h2>Transaksi Terbaru</h2>
        <a href="laporan.php" class="btn btn-secondary btn-sm">Lihat Semua</a>
      </div>
      <table>
        <thead><tr><th>#</th><th>Pelanggan</th><th>Total</th></tr></thead>
        <tbody>
        <?php foreach ($transaksiTerbaru as $t): ?>
          <tr>
            <td>#<?= $t['PenjualanID'] ?></td>
            <td><?= htmlspecialchars($t['NamaPelanggan'] ?? 'Umum') ?></td>
            <td><strong>Rp <?= number_format($t['TotalHarga'],0,',','.') ?></strong></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="card">
      <div class="card-header"><h2><i class="ph-bold ph-warning"></i> Stok Menipis (&lt;10)</h2></div>
      <table>
        <thead><tr><th>Produk</th><th>Sisa Stok</th></tr></thead>
        <tbody>
        <?php if (empty($stokMenipis)): ?>
          <tr><td colspan="2" style="text-align:center;color:var(--muted);padding:24px">Semua stok aman <i class="ph-bold ph-check-circle" style="color:var(--success);"></i></td></tr>
        <?php else: ?>
          <?php foreach ($stokMenipis as $s): ?>
            <tr>
              <td><?= htmlspecialchars($s['NamaProduk']) ?></td>
              <td><span class="badge badge-red"><?= $s['Stok'] ?></span></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
