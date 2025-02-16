<?php

/**
 * Class Database
 * 
 * This class handles the connection to a PostgreSQL database using PDO.
 * It provides methods to establish a connection and create a users table.
 */
class Database {
    /**
     * @var PDO|null $pdo The PDO instance for database connection
     */
    private $pdo;

    /**
     * Database constructor.
     * 
     * Initializes a new PDO connection to the PostgreSQL database.
     * 
     * @param string $host The database host
     * @param string $dbname The name of the database
     * @param string $user The database username
     * @param string $password The database password
     */
    
    public function __construct($host, $dbname, $user, $password) {
        $dsn = "pgsql:host=$host;dbname=$dbname";
        try {
            $this->pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (PDOException $e) {
            // Output error message and terminate script if connection fails
            die("Database connection failed: " . $e->getMessage() . "\n");
        }
    }

    /**
     * Get the PDO connection instance.
     * 
     * @return PDO|null The PDO connection instance
     */
    public function getConnection() {
        return $this->pdo;
    }

    /**
     * Create the 'users' table if it does not already exist.
     * 
     * The table contains:
     * - id (Primary Key, Auto-incremented)
     * - name (VARCHAR 100, Not Null)
     * - surname (VARCHAR 100, Not Null)
     * - email (VARCHAR 100, Unique, Not Null)
     * 
     * @return void
     */
    public function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            surname VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL
        );";

        // Execute SQL query to create the table
        $this->pdo->exec($sql);
        echo "Table 'users' created successfully.\n";
    }
}

?>
