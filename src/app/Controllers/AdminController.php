<?php

namespace App\Controllers;

use App\Models\User;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct(); // Assuming the parent constructor initializes Twig
    }

    public function createAdminUser()
    {
        // Only run this if you're sure there's no admin or you're doing it from a secure environment
        $email = "admin@example.com"; // Replace with the desired admin email
        $password = "1"; // Replace with a strong password

        // Check if an admin user already exists to prevent creating multiple admin users
        $existingAdmin = User::findByEmail($email);
        if ($existingAdmin) {
            // Admin user already exists, handle this case appropriately, perhaps log the attempt
            echo "An admin user already exists.";
            return;
        }

        // Create a new user instance and set the admin flag
        $user = new User();
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT); // Always hash passwords!
        $user->is_admin = true; // Ensure your User model supports an 'is_admin' property or similar

        // Attempt to save the new admin user
        if ($user->createAdmin()) {
            // Admin user created successfully
            echo "Admin user created successfully.";
        } else {
            // Failed to create admin user, handle this case appropriately
            echo "Failed to create admin user.";
        }
    }

    public function loginForm()
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/admin'); // Already logged in
        } else {
            $this->render('login.twig');
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = User::findByEmail($email); // Assumes User model has a findByEmail method

            if ($user && password_verify($password, $user->password)) {
                $_SESSION['user_id'] = $user->id; // Set user session
                $_SESSION['is_admin'] = $user->is_admin; // Track if user is an admin

                $this->redirect('/admin'); // Redirect to the admin dashboard
            } else {
                // Login failed, render login form with error
                $this->render('/login.twig', ['error' => 'Invalid credentials. Please try again.']);
            }
        } else {
            $this->loginForm();
        }
    }

    public function logout()
    {
        session_start();
        unset($_SESSION['user_id']);
        unset($_SESSION['is_admin']); // Clear admin flag
        session_destroy(); // Optional: completely destroy the session

        $this->redirect('/login'); // Redirect to the login page
    }

    // Helper method for redirects
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
}
