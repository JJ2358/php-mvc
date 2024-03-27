<?php

namespace App\Controllers;

use App\Models\User;
use App\DB\DBConnection;
use PDO;
use App\Services\JobApiService;

class AdminController extends Controller
{
    private $pdo;
    private $userModel;


    public function __construct()
    {
        parent::__construct(); // Assuming the parent constructor initializes Twig
        $this->pdo = (new DBConnection())->getConnection();
        $this->userModel = new User(); // Initialize the User model
    }

    public function adminDashboard() {
        // Check if user is an admin and logged in
        if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
            $_SESSION['error'] = 'Access denied. Please log in as an admin.';
            $this->redirect('/login');
            return;
        }

        // Render the admin dashboard view
        $this->render('admin_dashboard.twig', [
            'flash_message' => $_SESSION['flash_message'] ?? null,
        ]);
        unset($_SESSION['flash_message']); // Clear the flash message after displaying
    }


    public function createAdminUser()
    {
        // Define admin user credentials
        $email = "admin@example.com";
        $password = "1"; // Use a strong, secure password in production

        // Initialize the User model and check if an admin already exists
        $userModel = new User();
        $existingAdmin = $userModel->findByEmail($email);

        if ($existingAdmin) {
            // Admin already exists
            $_SESSION['flash_message'] = "An admin user already exists.";
            $this->redirect('/login');
            return;
        }

        // Attempt to create a new admin user
        $wasCreated = $userModel->createUser([
            'email' => $email,
            'password' => $password,
        ]);

        if ($wasCreated) {
            // Admin creation successful
            $_SESSION['flash_message'] = "Admin user created successfully.";
        } else {
            // Admin creation failed
            $_SESSION['flash_message'] = "Failed to create admin user.";
        }

        $this->redirect('/login');
    }

    public function loginForm()
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/admin');
        } else {
            $this->render('login.twig');
        }
    }
    public function findById($id): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function login() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Validate inputs
            if (empty($email) || empty($password)) {
                // Set error message
                $_SESSION['error'] = 'Email and password are required.';
                $this->render('login.twig', ['error' => $_SESSION['error']]);
                return;
            }

            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $this->redirect('/admin');
            } else {
                // Provide feedback that login failed
                $this->render('login.twig', ['error' => 'Invalid credentials. Please try again.']);
            }
        } else {
            $this->loginForm(); // Show the login form if not a POST request
        }
    }
    public function logout()
    {
        unset($_SESSION['user_id']);
        session_destroy();
        $this->redirect('/login');
    }

    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
    protected function flashAndRedirect($message, $redirectPath)
    {
        $_SESSION['flash_message'] = $message;
        $this->redirect($redirectPath);
    }
}
