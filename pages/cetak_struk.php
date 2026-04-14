<?php
require_once '../classes/Auth.php';
Auth::cekLogin();
require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    die("ID Transaksi tidak valid.");
}

$db = Database::getInstance()->getConn();

$stmt = $db->prepare(
    "SELECT p.*, pl.NamaPelanggan, u.NamaUser
     FROM penjualan p
     LEFT JOIN pelanggan pl ON p.PelangganID = pl.PelangganID
     LEFT JOIN users u ON p.UserID = u.UserID
     WHERE p.PenjualanID = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$transaksi = $stmt->get_result()->fetch_assoc();

if (!$transaksi) {
    die("Transaksi tidak ditemukan.");
}

$stmt = $db->prepare(
    "SELECT d.*, pr.NamaProduk, pr.Harga FROM detailpenjualan d
     JOIN produk pr ON d.ProdukID = pr.ProdukID
     WHERE d.PenjualanID = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Penjualan #<?= $transaksi['PenjualanID'] ?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            margin: 0;
            padding: 20px;
            background: #fff;
            color: #000;
        }
        .struk-container {
            width: 300px;
            margin: 0 auto;
            border: 1px dashed #000;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
        }
        .header p {
            margin: 0;
            font-size: 12px;
        }
        .info {
            margin-bottom: 15px;
            font-size: 12px;
        }
        .info table {
            width: 100%;
        }
        .info table td {
            padding: 2px 0;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 15px;
        }
        .items th, .items td {
            border-bottom: 1px dashed #000;
            padding: 5px 0;
        }
        .items th {
            text-align: left;
        }
        .items .right {
            text-align: right;
        }
        .items .center {
            text-align: center;
        }
        .total-area {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .total-area table {
            width: 100%;
        }
        .total-area table td {
            padding: 2px 0;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }
        @media print {
            body {
                padding: 0;
            }
            .struk-container {
                border: none;
                width: 100%;
            }
            @page {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="struk-container">
        <div class="header">
            <h2>POS APP</h2>
            <p>Struk Pembelian</p>
        </div>
        
        <div class="info">
            <table>
                <tr>
                    <td>No. Transaksi</td>
                    <td>: #<?= $transaksi['PenjualanID'] ?></td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>: <?= date('d/m/Y ', strtotime($transaksi['TanggalPenjualan'])) ?></td>
                </tr>
                <tr>
                    <td>Kasir</td>
                    <td>: <?= htmlspecialchars($transaksi['NamaUser'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td>Pelanggan</td>
                    <td>: <?= htmlspecialchars($transaksi['NamaPelanggan'] ?? 'Umum') ?></td>
                </tr>
            </table>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="center">Qty</th>
                    <th class="right">Harga</th>
                    <th class="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['NamaProduk']) ?></td>
                    <td class="center"><?= $item['JumlahProduk'] ?></td>
                    <td class="right"><?= number_format($item['Harga'], 0, ',', '.') ?></td>
                    <td class="right"><?= number_format($item['Subtotal'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-area">
            <table>
                <tr>
                    <td>Total</td>
                    <td class="right">Rp <?= number_format($transaksi['TotalHarga'], 0, ',', '.') ?></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Terima Kasih Atas Kunjungan Anda</p>
        </div>
    </div>
    <script>
        window.addEventListener('load', () => {
            window.print();
        });
    </script>
</body>
</html>
