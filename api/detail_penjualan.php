<?php
require_once '../config/database.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { echo "Unauthorized"; exit; }

$db = Database::getInstance()->getConn();
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare(
    "SELECT d.*, pr.NamaProduk, pr.Harga FROM detailpenjualan d
     JOIN produk pr ON d.ProdukID = pr.ProdukID
     WHERE d.PenjualanID = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<table style="width:100%;border-collapse:collapse;font-size:13px">
  <thead>
    <tr style="background:#f0f4ff">
      <th style="padding:8px 12px;text-align:left">Produk</th>
      <th style="padding:8px 12px">Harga Satuan</th>
      <th style="padding:8px 12px">Jumlah</th>
      <th style="padding:8px 12px">Subtotal</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td style="padding:8px 12px"><?= htmlspecialchars($r['NamaProduk']) ?></td>
      <td style="padding:8px 12px;text-align:center">Rp. <?= number_format($r['Harga'],0,',','.') ?></td>
      <td style="padding:8px 12px;text-align:center"><?= $r['JumlahProduk'] ?></td>
      <td style="padding:8px 12px;text-align:right"><strong>Rp. <?= number_format($r['Subtotal'],0,',','.') ?></strong></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>