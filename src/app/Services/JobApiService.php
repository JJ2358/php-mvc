<?php

namespace App\Services;

use PDO;
use App\DB\DBConnection;
use App\Models\Job;

/**
 * JobApiService handles fetching and storing job postings from an external API.
 */
class JobApiService {
    /**
     * The API URL from where to fetch job postings.
     *
     * @var string
     */
    protected $apiUrl = 'http://p2api.ryanmclaren.ca/api/job-postings';

    /**
     * Fetches job postings from the external API.
     *
     * This method uses cURL to retrieve job postings and returns an array
     * of jobs if the request is successful. Returns an empty array on error.
     *
     * @return array An array of jobs from the API, or an empty array on error.
     */
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

    /**
     * Saves fetched jobs to the database.
     *
     * Iterates through each job fetched from the API and saves it to the database
     * if it does not already exist.
     *
     * @param array $jobs An array of jobs to be saved.
     */
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
