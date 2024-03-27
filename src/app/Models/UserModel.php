<?php

namespace App\Models;

use App\DB\DBConnection;
use PDO;

class UserModel {
    private $pdo;

    public function __construct() {
        $this->pdo = (new DBConnection())->getConnection();
    }

    public function ensureAdminUserExists() {
        // Check if an admin user already exists
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM users WHERE email = 'admin@example.com'");
        $exists = $stmt->fetchColumn() > 0;

        if (!$exists) {
            // No admin user, so create one
            $passwordHash = password_hash('your_secure_password', PASSWORD_DEFAULT);
            $insertStmt = $this->pdo->prepare("INSERT INTO users (email, password_hash) VALUES (:email, :password_hash)");
            $insertStmt->execute([
                ':email' => 'admin@example.com',
                ':password_hash' => $passwordHash
            ]);

            echo "Admin user created.\n";
        } else {
            echo "Admin user already exists.\n";
        }
    }
    public function createUser($email, $password) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (email, password_hash) VALUES (:email, :password_hash)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password_hash', $passwordHash);
        return $stmt->execute();
    }

    // Add more user related methods as necessary
}
