<?php

namespace App\Controllers;

use App\Models\Job;
use App\Services\JobApiService;

class HomeController extends Controller {
    public function index() {
        $jobModel = new Job();
        $perPage = 5; // Jobs per page
        $currentPage = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
        $totalJobs = $jobModel->getCount();
        $totalPages = ceil($totalJobs / $perPage);
        $offset = ($currentPage - 1) * $perPage;

        $jobs = $jobModel->getJobs($offset, $perPage); // Adjusted method for pagination

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
}
