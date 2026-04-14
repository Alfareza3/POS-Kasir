<?php
// classes/Produk.php
require_once __DIR__ . '/../config/database.php';

class Produk {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConn();
    }

    public function getAll(): array {
        $result = $this->conn->query("SELECT * FROM produk ORDER BY NamaProduk");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById(int $id): ?array {
        $stmt = $this->conn->prepare("SELECT * FROM produk WHERE ProdukID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }

    public function tambah(string $nama, float $harga, int $stok): bool {
        $stmt = $this->conn->prepare(
            "INSERT INTO produk (NamaProduk, Harga, Stok) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sdi", $nama, $harga, $stok);
        return $stmt->execute();
    }

    public function edit(int $id, string $nama, float $harga, int $stok): bool {
        $stmt = $this->conn->prepare(
            "UPDATE produk SET NamaProduk=?, Harga=?, Stok=? WHERE ProdukID=?"
        );
        $stmt->bind_param("sdii", $nama, $harga, $stok, $id);
        return $stmt->execute();
    }

    public function hapus(int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM produk WHERE ProdukID=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function kurangiStok(int $id, int $jumlah): bool {
        $stmt = $this->conn->prepare(
            "UPDATE produk SET Stok = Stok - ? WHERE ProdukID = ? AND Stok >= ?"
        );
        $stmt->bind_param("iii", $jumlah, $id, $jumlah);
        return $stmt->execute() && $stmt->affected_rows > 0;
    }
}
