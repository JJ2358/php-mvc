<?php

namespace App\Models;

use PDO;
use App\DB\DBConnection;

/**
 * User model for handling user-related database operations.
 */
class User
{
    public $id;
    public $email;
    public $password;

    /**
     * @var PDO An instance of the PDO class for database connections.
     */
    private $pdo;

    /**
     * User constructor.
     * Initializes the database connection.
     */
    public function __construct()
    {
        $this->pdo = (new DBConnection())->getConnection();
    }

    /**
     * Creates a new user in the database.
     *
     * @param array $userData An associative array containing 'email', 'password_hash', and 'is_admin' keys.
     * @return bool True on success, false on failure.
     */
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
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves a user by their ID.
     *
     * @param int $id The user's ID.
     * @return array|null The user data as an associative array, or null if not found.
     */
    public function findById($id): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Retrieves a user by their email address.
     *
     * @param string $email The user's email address.
     * @return array|null The user data as an associative array, or null if not found.
     */
    public function findByEmail(string $email): ?array {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
        } catch (\PDOException $e) {
            error_log('PDOException - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Saves a new user to the database.
     *
     * @param array $data An associative array containing 'email' and 'password' keys.
     * @return bool True on success, false on failure.
     */
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

}
