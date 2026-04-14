<?php
require_once __DIR__ . '/../config/database.php';

class Auth {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConn();
    }

    public function login(string $username, string $password): bool {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE Username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $valid = false;
            if (password_verify($password, $row['Password'])) {
                $valid = true;
            } elseif ($password === $row['Password']) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $upd = $this->conn->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
                $upd->bind_param("si", $newHash, $row['UserID']);
                $upd->execute();
                $valid = true;
            }

            if ($valid) {
                $_SESSION['user_id']   = $row['UserID'];
                $_SESSION['username']  = $row['Username'];
                $_SESSION['nama']      = $row['NamaUser'];
                $_SESSION['role']      = $row['Role'];
                return true;
            }
        }
        return false;
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header("Location: index.php");
        exit;
    }

    public static function cekLogin(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../index.php");
            exit;
        }
    }

    public static function cekAdmin(): void {
        self::cekLogin();
        if ($_SESSION['role'] !== 'administrator') {
            header("Location: dashboard.php?error=akses_ditolak");
            exit;
        }
    }

    public function register(string $nama, string $username, string $password, string $role): bool {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare(
            "INSERT INTO users (NamaUser, Username, Password, Role) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $nama, $username, $hashed, $role);
        return $stmt->execute();
    }

    public function getAll(): array {
        return $this->conn->query("SELECT * FROM users ORDER BY Role, NamaUser")->fetch_all(MYSQLI_ASSOC);
    }

    public function getById(int $id): ?array {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE UserID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?: null;
    }

    public function edit(int $id, string $nama, string $username, string $password, string $role): bool {
        if ($password === '') {
            $stmt = $this->conn->prepare("UPDATE users SET NamaUser=?, Username=?, Role=? WHERE UserID=?");
            $stmt->bind_param("sssi", $nama, $username, $role, $id);
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE users SET NamaUser=?, Username=?, Password=?, Role=? WHERE UserID=?");
            $stmt->bind_param("ssssi", $nama, $username, $hashed, $role, $id);
        }
        return $stmt->execute();
    }

    public function hapus(int $id): bool {
        if ($_SESSION['user_id'] == $id) return false;
        $stmt = $this->conn->prepare("DELETE FROM users WHERE UserID = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}