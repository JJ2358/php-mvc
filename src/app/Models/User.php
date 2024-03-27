<?php

namespace App\Models;

use PDO;
use App\DB\DBConnection;

class User
{
    public $id;
    public $email;
    public $password;
    public $is_admin;

    private $pdo;

    public function __construct()
    {
        $this->pdo = (new DBConnection())->getConnection();
    }

    public function createAdmin()
    {
        // Use a prepared statement for security
        $stmt = $this->pdo->prepare("INSERT INTO users (email, password, is_admin) VALUES (:email, :password, :is_admin)");

        return $stmt->execute([
            ':email' => $this->email,
            ':password' => $this->password, // Assume this is already hashed
            ':is_admin' => $this->is_admin,
        ]);
    }

    public static function findByEmail($email)
    {
        $pdo = (new DBConnection())->getConnection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch();
    }

    // Add more methods as needed
}
