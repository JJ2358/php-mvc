<?php

namespace App\Services;

class AuthService {
    public function login($email, $password) {
        // Validate credentials (to be replaced with a real validation logic)
        return $email === 'admin@example.com' && $password === 'password';
    }

    public function isLoggedIn() {
        // Check if user session is set (to be replaced with real session checks)
        return isset($_SESSION['user']);
    }
}
