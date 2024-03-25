<?php

declare(strict_types=1);

namespace App\DB;

use PDO;

class DBConnection
{
    private $connection;

    public function __construct()
    {
        // Use getenv to retrieve the environment variable from .env file
        $password = getenv('MYSQL_ROOT_PASSWORD');

        // Since you haven't defined other DB parameters in .env,
        // we are using default values that match your Docker setup
        $host = 'mysql-server'; // This should match the service name in docker-compose
        $database = 'job_board'; // This is the database name you have in your docker-compose
        $user = 'root'; // Default MySQL user

        // Set the DSN for the PDO connection
        $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";

        // Create the PDO connection
        $this->connection = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
