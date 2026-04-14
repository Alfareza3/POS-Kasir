<?php
// classes/Penjualan.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Produk.php';

class Penjualan {
    private $conn;
    private $produk;

    public function __construct() {
        $this->conn   = Database::getInstance()->getConn();
        $this->produk = new Produk();
    }

    public function getAll(): array {
        $sql = "SELECT p.*, pl.NamaPelanggan, u.NamaUser
                FROM penjualan p
                LEFT JOIN pelanggan pl ON p.PelangganID = pl.PelangganID
                LEFT JOIN users u ON p.UserID = u.UserID
                ORDER BY p.TanggalPenjualan DESC, p.PenjualanID DESC";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function getDetail(int $penjualanId): array {
        $stmt = $this->conn->prepare(
            "SELECT d.*, pr.NamaProduk FROM detailpenjualan d
             JOIN produk pr ON d.ProdukID = pr.ProdukID
             WHERE d.PenjualanID = ?"
        );
        $stmt->bind_param("i", $penjualanId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * $items = [['produk_id'=>1,'jumlah'=>2], ...]
     */
    public function buat(int $pelangganId, int $userId, array $items): bool|int {
        $this->conn->begin_transaction();
        try {
            $total = 0;
            $details = [];
            foreach ($items as $item) {
                $p = $this->produk->getById((int)$item['produk_id']);
                if (!$p || $p['Stok'] < $item['jumlah']) {
                    throw new Exception("Stok tidak mencukupi: " . ($p['NamaProduk'] ?? ''));
                }
                $subtotal  = $p['Harga'] * $item['jumlah'];
                $total    += $subtotal;
                $details[] = ['produk_id'=>$p['ProdukID'],'jumlah'=>$item['jumlah'],'subtotal'=>$subtotal];
            }

            $tanggal = date('Y-m-d');
            $stmt = $this->conn->prepare(
                "INSERT INTO penjualan (TanggalPenjualan, TotalHarga, PelangganID, UserID) VALUES (?,?,?,?)"
            );
            $stmt->bind_param("sdii", $tanggal, $total, $pelangganId, $userId);
            $stmt->execute();
            $penjualanId = $this->conn->insert_id;

            foreach ($details as $d) {
                $stmt2 = $this->conn->prepare(
                    "INSERT INTO detailpenjualan (PenjualanID, ProdukID, JumlahProduk, Subtotal) VALUES (?,?,?,?)"
                );
                $stmt2->bind_param("iiid", $penjualanId, $d['produk_id'], $d['jumlah'], $d['subtotal']);
                $stmt2->execute();
                $this->produk->kurangiStok($d['produk_id'], $d['jumlah']);
            }

            $this->conn->commit();
            return $penjualanId;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function hapus(int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM penjualan WHERE PenjualanID=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
