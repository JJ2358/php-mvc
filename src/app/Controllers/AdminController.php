<?php

namespace App\Controllers;

use App\Models\User;
use App\DB\DBConnection;
use PDO;

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

    public function createUser(array $data): ?array {
        try {
            $sql = "INSERT INTO users (email, password_hash) VALUES (:email, :password_hash)";
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':email' => $data['email'],
                ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            ]);

            $userId = $this->pdo->lastInsertId();
            return $this->findById($userId);
        } catch (\PDOException $e) {
            error_log('PDOException - ' . $e->getMessage());
            return null;
        }
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
        unset($_SESSION['is_admin']);
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
