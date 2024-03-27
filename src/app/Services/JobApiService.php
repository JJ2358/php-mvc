<?php

namespace App\Services;

use PDO;
use App\DB\DBConnection;
use App\Models\Job;

class JobApiService {
    protected $apiUrl = 'http://p2api.ryanmclaren.ca/api/job-postings';

    public function fetchJobs() {
        $curl = curl_init($this->apiUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            error_log("cURL Error: " . $err);
            return [];
        } else {
            $data = json_decode($response, true);
            return $data['data'] ?? [];
        }
    }
    public function saveJobsToDatabase($jobs) {
        $jobModel = new Job();

        foreach ($jobs as $job) {
            // Check if the job already exists in the database
            if (!$jobModel->doesJobExist($job)) {
                $jobModel->save([
                    'title' => $job['title'],
                    'description' => $job['description'],
                    'location' => $job['location'],
                    'start_date' => $job['start_date'],
                    'contact_email' => $job['contact_email'],
                ]);
            }
        }
    }
}
