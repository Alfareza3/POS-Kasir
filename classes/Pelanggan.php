<?php
require_once __DIR__ . '/../config/database.php';

class Pelanggan {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConn();
    }

    public function getAll(): array {
        return $this->conn->query("SELECT * FROM pelanggan ORDER BY NamaPelanggan")
                          ->fetch_all(MYSQLI_ASSOC);
    }

    public function getById(int $id): ?array {
        $stmt = $this->conn->prepare("SELECT * FROM pelanggan WHERE PelangganID=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function tambah(string $nama, string $alamat, string $telepon): bool {
        $stmt = $this->conn->prepare(
            "INSERT INTO pelanggan (NamaPelanggan, Alamat, NomorTelepon) VALUES (?,?,?)"
        );
        $stmt->bind_param("sss", $nama, $alamat, $telepon);
        return $stmt->execute();
    }

    public function edit(int $id, string $nama, string $alamat, string $telepon): bool {
        $stmt = $this->conn->prepare(
            "UPDATE pelanggan SET NamaPelanggan=?, Alamat=?, NomorTelepon=? WHERE PelangganID=?"
        );
        $stmt->bind_param("sssi", $nama, $alamat, $telepon, $id);
        return $stmt->execute();
    }

    public function hapus(int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM pelanggan WHERE PelangganID=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
