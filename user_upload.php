<?php

/**
 * Require necessary files for database connection and user upload functionality.
 */
require_once 'Database.php';
require_once 'Command_rules.php';

/**
 * Define command line options.
 *
 * -u : PostgreSQL username (short option)
 * -p : PostgreSQL password (short option)
 * -h : PostgreSQL host (short option, default is localhost)
 * --file : Specify the CSV file to process
 * --create_table : Create the users table before processing data
 * --dry_run : Run the script without inserting data (for testing purposes)
 * --help : Display usage instructions
 */
$shortopts  = "u:p:h:"; 
$longopts  = ["file:", "create_table", "dry_run", "help"];
$options = getopt($shortopts, $longopts);

/**
 * Display help message and exit if --help option is provided.
 */
if (isset($options['help'])) {
    echo "Usage: php user_upload.php --file [filename.csv] -u [username] -p [password] -h [host] [--create_table] [--dry_run]\n";
    exit;
}

/**
 * Ensure PostgreSQL username and password are provided.
 */
if (!isset($options['u']) || !isset($options['p']) || empty($options['u']) || empty($options['p'])) {
    die("Error: PostgreSQL username (-u) and password (-p) are required.\n");
}

/**
 * Set database connection parameters.
 *
 * @var string $host
 * @var string $user
 * @var string $password
 * @var string $dbname
 */
$host = $options['h'] ?? 'localhost';
$user = $options['u'];
$password = $options['p'];
$dbname = 'testdb2';

/**
 * Establish database connection.
 *
 * @var Database $db
 * @var PDO $pdo
 */
$db = new Database($host, $dbname, $user, $password);
$pdo = $db->getConnection();

/**
 * Create table if --create_table option is provided, then exit.
 */
if (isset($options['create_table'])) {
    $db->createTable();
    exit;
}

/**
 * Ensure a CSV file is specified.
 */
if (!isset($options['file']) || empty($options['file'])) {
    die("Error: No file specified. Use --file [filename.csv]\n");
}

/**
 * @var string $file
 * @var bool $dryRun
 */
$file = $options['file'];
$dryRun = isset($options['dry_run']);

/**
 * Process the CSV file using the Command_rules class.
 *
 * @var Command_rules $uploader
 */
$uploader = new Command_rules($pdo, $dryRun);
$uploader->processFile($file);

?>
