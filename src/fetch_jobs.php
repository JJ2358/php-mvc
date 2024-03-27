<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\JobApiService;

// Instantiate and use JobApiService to fetch and save jobs
$jobService = new JobApiService();
$jobs = $jobService->fetchJobs();
$jobService->saveJobsToDatabase($jobs);

echo "Jobs fetched and saved successfully.\n";
