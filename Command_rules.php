<?php
/**
 * Class Command_rules
 * 
 * This class processes a CSV file and inserts user data into a PostgreSQL database.
 * It can operate in a dry-run mode where no actual insertions occur.
 */
class Command_rules {
    /**
     * @var PDO $pdo The PDO instance for database connection
     */
    private PDO $pdo;

    /**
     * @var bool $dryRun Flag to determine if operations should be executed or just simulated
     */
    private bool $dryRun;

    /**
     * Command_rules constructor.
     * 
     * @param PDO $pdo The database connection instance
     * @param bool $dryRun Whether to run in dry-run mode (default: false)
     */
    public function __construct(PDO $pdo, bool $dryRun = false) {
        $this->pdo = $pdo;
        $this->dryRun = $dryRun;
    }

    /**
     * Processes a CSV file and inserts valid user data into the database.
     * 
     * @param string $file The path to the CSV file
     * @return void
     */
    public function processFile(string $file): void {
        if (!file_exists($file)) {
            die("Error: File not found: $file\n");
        }

        if (($handle = fopen($file, "r")) !== FALSE) {
            fgetcsv($handle); // Skip CSV header
            while (($data = fgetcsv($handle)) !== FALSE) {
                $this->processRow($data);
            }
            fclose($handle);
        } else {
            die("Error: Unable to open file.\n");
        }
    }

    /**
     * Processes a single row of user data.
     * 
     * @param array $data The user data row (name, surname, email)
     * @return void
     */
    private function processRow(array $data): void {
        [$name, $surname, $email] = $data;

        $name = ucfirst(strtolower(trim($name)));
        $surname = ucfirst(strtolower(trim($surname)));
        $email = strtolower(trim($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email: $email\n";
            return;
        }

        if (!$this->dryRun) {
            $stmt = $this->pdo->prepare("INSERT INTO users (name, surname, email) VALUES (?, ?, ?) ON CONFLICT (email) DO NOTHING");
            $stmt->execute([$name, $surname, $email]);
        }
        echo "Processed: $name $surname <$email>\n";
    }
}
