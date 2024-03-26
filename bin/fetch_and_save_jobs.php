<?php

require_once 'vendor/autoload.php'; // Adjust the path as necessary

use App\Services\JobApiService;
use App\Models\Job;

$apiUrl = 'http://p2api.ryanmclaren.ca/api/job-postings';
$jobApiService = new JobApiService($apiUrl);
$jobsData = $jobApiService->fetchJobs();

$jobModel = new Job();

foreach ($jobsData as $jobData) {
    // Assuming the API returns data in the exact format your database expects,
    // otherwise, you may need to transform $jobData here.
    $result = $jobModel->save([
        'title' => $jobData['title'],
        'description' => $jobData['description'],
        'location' => $jobData['location'],
        'start_date' => $jobData['start_date'], // Make sure the format matches your DB format
        'contact_email' => $jobData['contact_email'],
    ]);

    if ($result) {
        echo "Job saved: " . $jobData['title'] . "\n";
    } else {
        echo "Failed to save job: " . $jobData['title'] . "\n";
    }
}
