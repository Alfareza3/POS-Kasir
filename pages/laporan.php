<?php
require_once '../classes/Auth.php';
Auth::cekLogin();
require_once '../config/database.php';
require_once '../classes/Penjualan.php';

$db = Database::getInstance()->getConn();
$penjualanObj = new Penjualan();

$dari  = $_GET['dari']  ?? date('Y-m-01');
$sampai= $_GET['sampai']?? date('Y-m-d');

$stmt = $db->prepare(
    "SELECT p.*, pl.NamaPelanggan, u.NamaUser
     FROM penjualan p
     LEFT JOIN pelanggan pl ON p.PelangganID = pl.PelangganID
     LEFT JOIN users u ON p.UserID = u.UserID
     WHERE p.TanggalPenjualan BETWEEN ? AND ?
     ORDER BY p.TanggalPenjualan DESC, p.PenjualanID DESC"
);
$stmt->bind_param("ss", $dari, $sampai);
$stmt->execute();
$list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$totalPendapatan = array_sum(array_column($list, 'TotalHarga'));

include '../includes/header.php';
?>
<div class="topbar"><h1><i class="ph-bold ph-chart-line-up"></i> Laporan Penjualan</h1></div>
<div class="content">

  <div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 24px">
    <form method="GET" style="display:flex;gap:16px;align-items:end;flex-wrap:wrap">
      <div class="form-group" style="margin:0">
        <label>Dari Tanggal</label>
        <input type="date" name="dari" class="form-control" value="<?= $dari ?>">
      </div>
      <div class="form-group" style="margin:0">
        <label>Sampai Tanggal</label>
        <input type="date" name="sampai" class="form-control" value="<?= $sampai ?>">
      </div>
      <button class="btn btn-primary"><i class="ph-bold ph-magnifying-glass"></i> Filter</button>
    </form>
    </div>
  </div>

  <div class="stat-grid" style="margin-bottom:20px">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="ph-fill ph-receipt"></i></div>
      <div><div class="stat-label">Total Transaksi</div><div class="stat-value"><?= count($list) ?></div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon green"><i class="ph-fill ph-currency-circle-dollar"></i></div>
      <div><div class="stat-label">Total Pendapatan</div>
        <div class="stat-value">Rp <?= number_format($totalPendapatan,0,',','.') ?></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon amber"><i class="ph-fill ph-calendar"></i></div>
      <div><div class="stat-label">Periode</div>
        <div style="font-size:13px;font-weight:700"><?= date('d/m/Y',strtotime($dari)) ?> – <?= date('d/m/Y',strtotime($sampai)) ?></div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Detail Transaksi</h2>
      <!-- <a href="#" onclick="window.print()" class="btn btn-secondary btn-sm"><i class="ph-bold ph-printer"></i> Cetak</a> -->
    </div>
    <table>
      <thead>
        <tr>
          <th>#ID</th><th>Tanggal</th><th>Pelanggan</th>
          <th>Kasir</th><th>Total</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($list)): ?>
        <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--muted)">
          Tidak ada data pada periode ini.
        </td></tr>
      <?php else: ?>
        <?php foreach ($list as $p): ?>
          <tr>
            <td><strong>#<?= $p['PenjualanID'] ?></strong></td>
            <td><?= date('d/m/Y', strtotime($p['TanggalPenjualan'])) ?></td>
            <td><?= htmlspecialchars($p['NamaPelanggan'] ?? 'Umum') ?></td>
            <td><?= htmlspecialchars($p['NamaUser'] ?? '-') ?></td>
            <td><strong>Rp <?= number_format($p['TotalHarga'],0,',','.') ?></strong></td>
            <td style="display:flex;gap:8px">
              <button onclick="lihatDetail(<?= $p['PenjualanID'] ?>)"
                      class="btn btn-secondary btn-sm"><i class="ph-bold ph-eye"></i> Detail</button>
              <a href="cetak_struk.php?id=<?= $p['PenjualanID'] ?>" target="_blank" class="btn btn-primary btn-sm"><i class="ph-bold ph-printer"></i> Cetak Struk</a>
            </td>
          </tr>
          <tr id="detail-<?= $p['PenjualanID'] ?>" style="display:none">
            <td colspan="6" style="background:#f8fafc;padding:0">
              <div style="padding:12px 24px" id="detail-content-<?= $p['PenjualanID'] ?>">
                Memuat...
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function lihatDetail(id) {
  const row = document.getElementById('detail-' + id);
  if (row.style.display === 'none') {
    row.style.display = '';
    fetch('../api/detail_penjualan.php?id=' + id)
      .then(r => r.text())
      .then(html => { document.getElementById('detail-content-' + id).innerHTML = html; });
  } else {
    row.style.display = 'none';
  }
}
</script>
<?php include '../includes/footer.php'; ?>
