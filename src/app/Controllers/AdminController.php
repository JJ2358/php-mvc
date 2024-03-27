<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\JobApiService;
use App\Models\UserModel;
use Exception;

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
    public function setupAdmin() {
        $userModel = new UserModel();
        $userModel->ensureAdminUserExists();
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
    public function createAdmin() {
        try {
            if (!$this->isAuthenticatedAdmin()) {
                // If not authenticated as admin, store an error message and redirect to login page
                $_SESSION['error'] = 'Unauthorized access.';
                header('Location: /login');
                exit;
            }

            $userModel = new UserModel();
            if ($userModel->ensureAdminUserExists()) {
                // If the admin user was successfully created, store a success message
                $_SESSION['message'] = 'Admin user created successfully.';
            } else {
                // If the admin user already exists, store a notice message
                $_SESSION['message'] = 'Admin user already exists.';
            }

            // Redirect to the admin dashboard with a message
            header('Location: /admin');
            exit;

        } catch (Exception $e) {
            // In case of any exception, store an error message and redirect or display an error page
            $_SESSION['error'] = 'Failed to create admin user: ' . $e->getMessage();
            header('Location: /error'); // Assuming you have an error route
            exit;
        }
    }
    private function isAuthenticatedAdmin() {
        // Example check: return true if a session variable for admin is set and true
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
    }

}
