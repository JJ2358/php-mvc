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
    public function save($jobData) {
        // Example SQL INSERT statement - adjust columns as necessary
        $sql = "INSERT INTO jobs (title, description, location, start_date, contact_email) VALUES (:title, :description, :location, :start_date, :contact_email)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':title' => $jobData['title'],
                ':description' => $jobData['description'],
                ':location' => $jobData['location'],
                ':start_date' => $jobData['start_date'],
                ':contact_email' => $jobData['contact_email'],
            ]);
            return true;
        } catch (\PDOException $e) {
            // Handle or log the error as appropriate
            error_log($e->getMessage());
            return false;
        }
    }


    public function getJobs($offset, $perPage) {
        $stmt = $this->pdo->prepare("SELECT * FROM jobs ORDER BY start_date DESC LIMIT :offset, :perPage");
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getCount() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM jobs");
            return (int)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Error in getCount: " . $e->getMessage());
            return 0;
        }
    }



    // You can add more methods here for insert, update, delete, etc., as needed.
}
