<?php

namespace App\Models;

use PDO;
use App\DB\DBConnection;

class User
{
    public $id;
    public $email;
    public $password;

    private $pdo;

    public function __construct()
    {
        $this->pdo = (new DBConnection())->getConnection();
    }

    public function createUser($userData) {
        $sql = "INSERT INTO users (email, password_hash, is_admin) VALUES (:email, :password_hash, :is_admin)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':email' => $userData['email'],
                ':password_hash' => $userData['password_hash'],
                ':is_admin' => $userData['is_admin']
            ]);
            return true;
        } catch (\PDOException $e) {
            // Log the error or handle it as needed
            error_log($e->getMessage());
            return false;
        }
    }

    public function findById($id): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByEmail(string $email): ?array {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
        } catch (\PDOException $e) {
            // Log error or handle it as per your application's error handling strategy
            error_log('PDOException - ' . $e->getMessage());
            return null;
        }
    }

    public function save(array $data): bool {
        try {
            $sql = "INSERT INTO users (email, password) VALUES (:email, :password)";
            $stmt = $this->pdo->prepare($sql);

            // Hash the password before saving
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
            $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log('PDOException - ' . $e->getMessage());
            return false;
        }
    }

    // Add more methods as needed
}
