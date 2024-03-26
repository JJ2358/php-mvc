<?php

namespace App\Controllers;

use App\Models\Job;

class JobController extends Controller {
    public function listJobs(): void {
        try {
            $jobModel = new Job();
            $jobs = $jobModel->getAll();
            $this->render('home.twig', ['jobs' => $jobs]);
        } catch (\Exception $e) {
            // Log the exception and render an error page or output an error message
            error_log($e->getMessage());
            $this->render('error.twig', ['errorMessage' => 'Error fetching job listings.']);
        }
    }

    public function showJob(int $id): void {
        try {
            $jobModel = new Job();
            $job = $jobModel->findById($id);

            if ($job) {
                $this->render('job.twig', ['job' => $job]);
            } else {
                throw new \Exception("Job not found"); // Use your custom exception or handle it accordingly
            }
        } catch (\Exception $e) {
            // Log the exception and render a not found or error page
            error_log($e->getMessage());
            $this->render('not_found.twig', ['errorMessage' => 'Job not found.']);
        }
    }

    // Add any additional methods you may need
}
