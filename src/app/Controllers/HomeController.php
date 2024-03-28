<?php

namespace App\Controllers;

use App\Models\Job;
use App\Services\JobApiService;

/**
 * HomeController handles the display of the homepage and job listings.
 */
class HomeController extends Controller {
    /**
     * Displays the homepage with job listings.
     *
     * Fetches jobs from an external API, saves them to the database,
     * and then retrieves a paginated list of jobs to display on the homepage.
     * Also determines if the current user is an admin for conditional display logic.
     */
    public function index(): void {
        $jobService = new JobApiService();
        $jobModel = new Job();

        // Fetch and save jobs from the API
        $jobs = $jobService->fetchJobs();
        if (!empty($jobs)) {
            $jobService->saveJobsToDatabase($jobs);
        }

        // Pagination setup
        $perPage = 5; // Jobs per page
        $currentPage = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
        $totalJobs = $jobModel->getCount();
        $totalPages = ceil($totalJobs / $perPage);
        $offset = ($currentPage - 1) * $perPage;

        // Fetch paginated jobs from the database
        $jobs = $jobModel->getJobs($offset, $perPage);
        $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];

        $this->render('home.twig', [
            'jobs' => $jobs,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'has_previous' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'previous_page' => $currentPage - 1,
            'next_page' => $currentPage + 1,
            'is_admin' => $isAdmin,
        ]);
    }

    /**
     * Renders the login page.
     */
    public function login(): void {
        $this->render('login.twig');
    }
}
