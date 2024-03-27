<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\JobApiService;

class AdminController extends Controller {
    private $authService;

    public function __construct() {
        parent::__construct();
        $this->authService = new AuthService();
    }

    public function login() {
        if ($this->authService->isLoggedIn()) {
            header('Location: /admin');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($this->authService->login($email, $password)) {
                header('Location: /admin');
                exit;
            } else {
                // Handle login failure
                $this->render('login.twig', ['error' => 'Invalid credentials.']);
                return;
            }
        }

        $this->render('login.twig');
    }

    public function admin() {
        if (!$this->authService->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        // Render the admin dashboard
        $this->render('admin.twig');
    }

    public function fetchJobs() {
        if (!$this->authService->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        $jobService = new JobApiService();
        $jobs = $jobService->fetchJobs();
        $jobService->saveJobsToDatabase($jobs);

        // Redirect to admin with a success message
        $_SESSION['message'] = "Jobs successfully fetched from API.";
        header('Location: /admin');
        exit;
    }
}
