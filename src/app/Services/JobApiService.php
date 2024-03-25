<?php

namespace App\Services;

class JobApiService {
    private $apiUrl;

    public function __construct(string $apiUrl) {
        $this->apiUrl = $apiUrl;
    }

    public function fetchJobs(): array {
        try {
            $json = file_get_contents($this->apiUrl);
            $jobs = json_decode($json, true);

            if (!is_array($jobs)) {
                throw new \Exception("Failed to decode JSON.");
            }

            return $jobs;
        } catch (\Exception $e) {
            // Log error or handle it as per your application's error handling policy
            error_log($e->getMessage());
            return [];
        }
    }
}
