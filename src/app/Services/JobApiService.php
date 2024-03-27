<?php

namespace App\Services;

use PDO;
use App\DB\DBConnection;

class JobApiService {
    protected $apiUrl = 'http://p2api.ryanmclaren.ca/api/job-postings';

    public function fetchJobs() {
        $json = file_get_contents($this->apiUrl);
        $data = json_decode($json, true);
        return $data['data'] ?? [];
    }

    public function saveJobsToDatabase($jobs) {
        $pdo = (new DBConnection())->getConnection();

        foreach ($jobs as $job) {
            $stmt = $pdo->prepare("INSERT INTO jobs (title, description, location, start_date, contact_email) VALUES (:title, :description, :location, :start_date, :contact_email)");
            $stmt->execute([
                ':title' => $job['title'],
                ':description' => $job['description'],
                ':location' => $job['location'],
                ':start_date' => date('Y-m-d', strtotime($job['start_date'])), // Ensure date format matches MySQL
                ':contact_email' => $job['contact_email'],
            ]);
        }
    }
}

// Usage
$jobService = new JobApiService();
$jobs = $jobService->fetchJobs();
$jobService->saveJobsToDatabase($jobs);
