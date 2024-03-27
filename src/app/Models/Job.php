<?php

namespace App\Models;

use App\DB\DBConnection;
use PDO;

class Job {
    private $pdo;

    public function __construct() {
        $this->pdo = (new DBConnection())->getConnection();
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
        $sql = "INSERT INTO jobs (title, description, location, start_date, contact_email) VALUES (:title, :description, :location, :start_date, :contact_email)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':title' => $jobData['title'],
            ':description' => $jobData['description'],
            ':location' => $jobData['location'],
            ':start_date' => $jobData['start_date'],
            ':contact_email' => $jobData['contact_email'],
        ]);
    }


    public function getJobs($offset, $perPage) {
        $sql = "SELECT * FROM jobs ORDER BY start_date DESC LIMIT :offset, :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getCount() {
        $sql = "SELECT COUNT(*) FROM jobs";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }

    public function getAll() {
        $stmt = $this->pdo->query('SELECT * FROM jobs ORDER BY start_date DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function doesJobExist($jobData) {
        $sql = "SELECT COUNT(*) FROM jobs WHERE title = :title AND location = :location AND start_date = :start_date AND contact_email = :contact_email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':title' => $jobData['title'],
            ':location' => $jobData['location'],
            ':start_date' => $jobData['start_date'],
            ':contact_email' => $jobData['contact_email']
        ]);
        return $stmt->fetchColumn() > 0;
    }


    // You can add more methods here for insert, update, delete, etc., as needed.
}
