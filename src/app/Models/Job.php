<?php

namespace App\Models;

use App\DB\DBConnection;
use PDO;

/**
 * Handles job-related database operations.
 *
 * Provides functionalities for fetching, saving, and checking jobs
 * in the database.
 */
class Job {
    /**
     * @var PDO Instance of PDO for database access.
     */
    private $pdo;

    /**
     * Initializes the database connection.
     */
    public function __construct() {
        $this->pdo = (new DBConnection())->getConnection();
    }

    /**
     * Finds a job by its ID.
     *
     * @param int $id The ID of the job to find.
     * @return array|null The job details as an associative array, or null if not found.
     */
    public function findById(int $id): ?array {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM jobs WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $job = $stmt->fetch(PDO::FETCH_ASSOC);
            return $job ?: null;
        } catch (\PDOException $e) {
            error_log('PDOException - ' . $e->getMessage(), 0);
            return null;
        }
    }

    /**
     * Saves job data to the database.
     *
     * @param array $jobData The job data to save.
     */
    public function save($jobData) {
        $sql = "INSERT INTO jobs (title, description, location, start_date, contact_email) VALUES (:title, :description, :location, :start_date, :contact_email)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($jobData);
    }

    /**
     * Retrieves a paginated list of jobs.
     *
     * @param int $offset The offset for pagination.
     * @param int $perPage The number of items per page.
     * @return array A list of jobs for the current page.
     */
    public function getJobs($offset, $perPage) {
        $sql = "SELECT * FROM jobs ORDER BY start_date DESC LIMIT :offset, :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Gets the total count of jobs.
     *
     * @return int The total number of jobs.
     */
    public function getCount() {
        $sql = "SELECT COUNT(*) FROM jobs";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }

    /**
     * Retrieves all jobs ordered by start date in descending order.
     *
     * @return array A list of all jobs.
     */
    public function getAll() {
        $stmt = $this->pdo->query('SELECT * FROM jobs ORDER BY start_date DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Checks if a job already exists in the database.
     *
     * @param array $jobData The job data to check.
     * @return bool True if the job exists, false otherwise.
     */
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

}
