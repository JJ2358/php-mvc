<?php

namespace App\Models;

use App\DB\DBConnection;
use PDOException;

class Job {
    private $dbConnection;

    public function __construct() {
        $this->dbConnection = (new DBConnection())->getConnection();
    }

    public function save(array $jobData): bool {
        try {
            // Assume 'job_id' is a unique identifier from the API for each job
            // Check if job already exists to prevent duplicates
            $existsQuery = "SELECT COUNT(*) FROM jobs WHERE job_id = :job_id";
            $stmt = $this->dbConnection->prepare($existsQuery);
            $stmt->execute([':job_id' => $jobData['job_id']]);

            if ($stmt->fetchColumn() > 0) {
                return false;
            }

            // Insert new job
            $sql = "INSERT INTO jobs (job_id, title, description, location, start_date, contact_email) VALUES (:job_id, :title, :description, :location, :start_date, :contact_email)";

            $stmt = $this->dbConnection->prepare($sql);

            $stmt->execute([
                ':job_id' => $jobData['job_id'],
                ':title' => $jobData['title'],
                ':description' => $jobData['description'],
                ':location' => $jobData['location'],
                ':start_date' => $jobData['start_date'],
                ':contact_email' => $jobData['contact_email'],
            ]);

            return true;
        } catch (PDOException $e) {
            // Log error or handle it as per your application's error handling policy
            error_log($e->getMessage());
            return false;
        }
    }
}
