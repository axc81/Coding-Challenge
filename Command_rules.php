<?php

/**
 * Command_rules - Handles the processing of user data from a CSV file.
 *
 * This class reads a CSV file, validates the data, and inserts it into a PostgreSQL database.
 * It also provides a dry-run mode to simulate the upload without making actual changes.
 */

class Command_rules
{
    private $pdo;      // PDO database connection
    private $dryRun;   // Dry-run mode (true = simulate, false = insert data)

    /**
     * Constructor to initialize database connection and dry-run mode.
     *
     * @param PDO $pdo Database connection instance.
     * @param bool $dryRun Flag to indicate dry-run mode.
     */
    public function __construct(PDO $pdo, bool $dryRun)
    {
        $this->pdo = $pdo;
        $this->dryRun = $dryRun;
    }

    /**
     * Processes the given CSV file and uploads valid data to the database.
     *
     * @param string $filename Path to the CSV file.
     */
    public function processFile(string $filename)
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            die("Error: Cannot read file $filename\n");
        }

        try {
            $handle = fopen($filename, "r");
            if ($handle === false) {
                throw new Exception("Unable to open file $filename");
            }

            if ($this->dryRun) {
                echo "Dry-run mode enabled. No data will be inserted into the database.\n";
            }

            // Skip the header row
            fgetcsv($handle, 1000, ",");

            // Read each line from CSV file
            while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                // Ignore empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Ensure row has exactly 3 columns (name, surname, email)
                if (count($row) < 3) {
                    echo "Invalid row (less than 3 columns): " . implode("|", $row) . "\n";
                    continue;
                }

                // Trim and sanitize inputs
                $name = trim($row[0]);
                $surname = trim($row[1]);
                $email = trim($row[2]);

                // Validate name and surname (only alphabetic characters allowed)
                if (!preg_match("/^[a-zA-Z\s]+$/", $name) || !preg_match("/^[a-zA-Z\s]+$/", $surname)) {
                    echo "Invalid name or surname: $name $surname\n";
                    continue;
                }

                // Validate email format and ensure it's not empty
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo "Invalid or empty email: $email\n";
                    continue;
                }

                // Normalize name and surname (capitalize first letter)
                $name = ucwords(strtolower($name));
                $surname = ucwords(strtolower($surname));

                // Check if email already exists
                $checkSql = "SELECT COUNT(*) FROM users WHERE email = :email";
                $checkStmt = $this->pdo->prepare($checkSql);
                $checkStmt->execute([':email' => $email]);
                $emailExists = $checkStmt->fetchColumn();

                if ($emailExists) {
                    echo "Duplicated: $name $surname <$email>\n";
                    continue;
                }

                // Check for dry_run mode
                if ($this->dryRun) {
                    echo "Processed: $name $surname <$email>\n";
                } else {
                    $this->insertUser($name, $surname, $email);
                }
            }

            fclose($handle);
        } catch (Exception $e) {
            echo "File processing error: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Inserts a new user into the database.
     *
     * @param string $name First name of the user.
     * @param string $surname Last name of the user.
     * @param string $email Email address of the user.
     */
    private function insertUser(string $name, string $surname, string $email)
    {
        // Insert new user
        $sql = "INSERT INTO users (name, surname, email) VALUES (:name, :surname, :email)";
        $stmt = $this->pdo->prepare($sql);

        try {
            $stmt->execute([
                ':name' => $name,
                ':surname' => $surname,
                ':email' => $email
            ]);
            echo "Inserted: $name $surname <$email>\n";
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage() . "\n";
        }
    }
}
