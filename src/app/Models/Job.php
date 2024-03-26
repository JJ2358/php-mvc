<?php

namespace App\Models;

use App\DB\DBConnection;
use PDO;

class Job {
    private $pdo;

    public function __construct() {
        $this->pdo = (new DBConnection())->getConnection();
    }

    public function getAll(): array {
        try {
            $stmt = $this->pdo->query('SELECT * FROM jobs ORDER BY start_date DESC');
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function findById(int $id): ?array {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM jobs WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $job = $stmt->fetch(PDO::FETCH_ASSOC);
            return $job ?: null;
        } catch (\PDOException $e) {
            // Error handling
            error_log('PDOException - ' . $e->getMessage(), 0);
            return null;
        }
    }
    public function save(array $jobData): bool {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO jobs (title, description, location, start_date, contact_email) VALUES (:title, :description, :location, :start_date, :contact_email)");

            $stmt->execute([
                ':title' => $jobData['title'],
                ':description' => $jobData['description'],
                ':location' => $jobData['location'],
                ':start_date' => date('Y-m-d', strtotime($jobData['start_date'])), // Ensuring the date format matches SQL
                ':contact_email' => $jobData['contact_email'],
            ]);

            return true;
        } catch (\PDOException $e) {
            // Ideally, log this error
            error_log($e->getMessage());
            return false;
        }
    }


    // You can add more methods here for insert, update, delete, etc., as needed.
}
