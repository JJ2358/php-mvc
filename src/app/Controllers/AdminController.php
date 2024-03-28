<?php

namespace App\Controllers;

use App\Models\User;
use App\DB\DBConnection;
use PDO;
use App\Services\JobApiService;
/**
 * Handles administrative actions such as managing users and jobs.
 */
class AdminController extends Controller
{
    /**
     * @var PDO Instance for database access.
     */
    private $pdo;
    /**
     * @var User Model for user-related database operations.
     */
    private $userModel;

    /**
     * Initializes the Controller by setting up the database connection
     * and the User model.
     */
    public function __construct()
    {
        parent::__construct(); // Assuming the parent constructor initializes Twig
        $this->pdo = (new DBConnection())->getConnection();
        $this->userModel = new User(); // Initialize the User model
    }

    /**
     * Displays the admin dashboard, checking for admin permissions.
     */
    public function adminDashboard() {
        // Check if user is an admin and logged in
        if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
            $_SESSION['error'] = 'Access denied. Please log in as an admin.';
            $this->redirect('/login');
            return;
        }

        // Render the admin dashboard view with is_admin
        $this->render('admin_dashboard.twig', [
            'flash_message' => $_SESSION['flash_message'] ?? null,
            'is_admin' => $_SESSION['is_admin']
        ]);
        unset($_SESSION['flash_message']); // Clear the flash message after displaying
    }

    /**
     * Attempts to create an admin user account.
     */
    public function createAdminUser() {
        $email = "admin@example.com";
        $password = "1"; // Replace with a secure password

        // Check if an admin user already exists
        $existingAdmin = $this->userModel->findByEmail($email);
        if ($existingAdmin) {
            $_SESSION['flash_message'] = "An admin user already exists.";
            $this->redirect('/login');
            return;
        }

        // Hash the password for security
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Attempt to create a new admin user
        // Adjust according to how your userModel->createUser method works
        $wasCreated = $this->userModel->createUser([
            'email' => $email,
            'password_hash' => $passwordHash,
            'is_admin' => 1 // Ensure this user is created as an admin
        ]);

        if ($wasCreated) {
            $_SESSION['flash_message'] = "Admin user created successfully.";
        } else {
            // Handle the failure case
            $_SESSION['flash_message'] = "Failed to create admin user.";
        }

        $this->redirect('/login');
    }

    /**
     * Displays the login form or redirects to the admin dashboard if already logged in.
     */
    public function loginForm()
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/admin');
        } else {
            $this->render('login.twig');
        }
    }

    /**
     * Finds a user by their ID.
     *
     * @param int $id User ID to search for.
     * @return array|null Returns user info if found, otherwise null.
     */
    public function findById($id): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    /**
     * Handles the login procedure including validation of inputs and credentials.
     */
    public function login() {
        // Check if the request method is POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Validate inputs
            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'Email and password are required.';
                $this->render('login.twig', ['error' => $_SESSION['error']]);
                return;
            }

            $user = $this->userModel->findByEmail($email);

            // Assuming your user model's `findByEmail` method returns false if no user is found
            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['is_admin'] = (bool)$user['is_admin']; // Cast is_admin to boolean

                // Redirect to the admin dashboard or home page based on user role
                if ($_SESSION['is_admin']) {
                    $this->redirect('/admin');
                } else {
                    $this->redirect('/');
                }
            } else {
                // Authentication failed
                $_SESSION['error'] = 'Invalid credentials. Please try again.';
                $this->render('login.twig', ['error' => $_SESSION['error']]);
            }
        } else {
            // For non-POST requests, just show the login form
            $this->render('login.twig');
        }
    }
    /**
     * Logs out the current user and clears the session.
     */
    public function logout() {
        // Unset all session variables
        $_SESSION = [];

        // If it's desired to kill the session, also delete the session cookie.
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();

        // Redirect to the login page
        $this->redirect('/login');
    }

    /**
     * Redirects to the specified URL.
     *
     * @param string $url The URL to redirect to.
     */
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
    /**
     * Sets a flash message and redirects to the specified path.
     *
     * @param string $message The message to set.
     * @param string $redirectPath The path to redirect to.
     */
    protected function flashAndRedirect($message, $redirectPath)
    {
        $_SESSION['flash_message'] = $message;
        $this->redirect($redirectPath);
    }
    /**
     * Fetches jobs from an external API and saves them to the database.
     */
    public function fetchAndSaveJobs() {
        if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
            $_SESSION['error'] = 'Access denied. Unauthorized action.';
            $this->redirect('/login');
            return;
        }

        $jobService = new JobApiService();
        $jobs = $jobService->fetchJobs();
        if (!empty($jobs)) {
            $jobService->saveJobsToDatabase($jobs);
            $_SESSION['flash_message'] = "Jobs successfully fetched from API and saved to the database.";
        } else {
            $_SESSION['flash_message'] = "Failed to fetch jobs from API or no new jobs available.";
        }

        $this->redirect('/admin');
    }

}
