<?php

namespace App\Controllers;

use App\Models\Job;
use App\Services\JobApiService;

class HomeController extends Controller {
    public function index() {
        $jobService = new JobApiService();
        $jobModel = new Job();

        // Optionally, fetch and save jobs from the API if needed.
        // This could be based on certain conditions, like a daily update, etc.
        // $jobs = $jobService->fetchJobs();
        // if (!empty($jobs)) {
        //     $jobService->saveJobsToDatabase($jobs); // Comment or adjust this line based on your application's needs
        // }

        // Pagination setup
        $perPage = 5; // Jobs per page
        $currentPage = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
        $totalJobs = $jobModel->getCount();
        $totalPages = ceil($totalJobs / $perPage);
        $offset = ($currentPage - 1) * $perPage;

        // Fetch paginated jobs
        $jobs = $jobModel->getJobs($offset, $perPage); // Use the adjusted method for pagination

        $this->render('home.twig', [
            'jobs' => $jobs,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'has_previous' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'previous_page' => $currentPage - 1,
            'next_page' => $currentPage + 1,
        ]);
    }
    public function login(): void
    {
        $this->render('login.twig');
    }
}
