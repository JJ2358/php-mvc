<?php

declare(strict_types=1);

namespace App\Models;

use App\DB\DBConnection;

/**
 * Base model class for managing database connections.
 *
 * This class serves as a foundation for all model classes in the application,
 * providing a shared database connection and common functionalities.
 */
class Model
{
    /**
     * @var DBConnection Holds the database connection object.
     */
    private DBConnection $DBConnection;

    /**
     * Constructs a new model instance, initializing the database connection.
     *
     * Note: The current constructor parameters ($lastName, $firstName, $address)
     * do not seem to be used within the constructor. Consider revising or removing
     * these parameters unless they serve a purpose not shown in this context.
     *
     * @param string|null $lastName Optional last name, not currently used.
     * @param string|null $firstName Optional first name, not currently used.
     * @param string|null $address Optional address, not currently used.
     */
    public function __construct(string $lastName = null, string $firstName = null, string $address = null)
    {
        $this->DBConnection = new DBConnection();
    }

    /**
     * Retrieves the DBConnection object.
     *
     * @return DBConnection The database connection.
     */
    public function getDBConnection(): DBConnection
    {
        return $this->DBConnection;
    }
}
