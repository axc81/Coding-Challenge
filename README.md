# Coding Challenge

## Overview

This project provides a command-line PHP script (**user\_upload.php**) that processes a CSV file containing user data and inserts it into a PostgreSQL database. The script ensures data validation and normalization before inserting it into the database.

## Features

- Reads user data from a CSV file
- Validates email addresses before inserting into the database
- Capitalizes names and surnames before inserting
- Supports a **dry-run mode** to test without modifying the database
- Allows creating the users table before inserting data
- Handles duplicate email entries
- Displays detailed error messages for invalid data

## Prerequisites

Ensure the following are installed before running the script:

- **PHP 8.3**
- **PostgreSQL 13 or higher**
- Required PHP extensions: `pdo_pgsql`

## Database Schema

The script creates a PostgreSQL table named `users` with the following structure:

| Column  | Type         | Constraints      |
| ------- | ------------ | ---------------- |
| id      | SERIAL       | PRIMARY KEY      |
| name    | VARCHAR(100) | NOT NULL         |
| surname | VARCHAR(100) | NOT NULL         |
| email   | VARCHAR(100) | UNIQUE, NOT NULL |

## Installation

1. Clone the repository:
   ```sh
   git clone <repository_url>
   cd <repository_folder>
   ```

## Usage

Run the script with the following command-line options:

```sh
php user_upload.php --file users.csv -u <username> -p <password> -h <host> [--create_table] [--dry_run] [--help]
```

### Command-Line Options

| Option           | Description                                                   |
| ---------------- | ------------------------------------------------------------- |
| `--file`         | Specifies the CSV file to process (e.g., `--file users.csv`)  |
| `--create_table` | Creates the `users` table in the database and exits           |
| `--dry_run`      | Runs the script without inserting data (for testing purposes) |
| `-u`             | PostgreSQL username                                           |
| `-p`             | PostgreSQL password                                           |
| `-h`             | PostgreSQL host (default: `localhost`)                        |
| `--help`         | Displays usage instructions                                   |

### Example Usage

#### 1. Create the `users` table:

```sh
php user_upload.php --create_table -u myuser -p mypassword -h localhost
```

#### 2. Run in dry-run mode (no database insertion):

```sh
php user_upload.php --file users.csv --dry_run -u myuser -p mypassword -h localhost
```

#### 3. Process CSV and insert data into the database:

```sh
php user_upload.php --file users.csv -u myuser -p mypassword -h localhost
```

## Error Handling

- If an email format is invalid, it will be skipped, and an error message will be displayed.
- If an email already exists in the database, the record will not be inserted, and a warning will be shown.
- If the CSV file is not readable or missing, an error message will be displayed.

## Version Control Guidelines

- Use Git for version control.
- Commits should reflect development progress, not just a final version.
- Avoid a single commit for the entire script; instead, use meaningful commits.

## License

This project is for educational and assessment purposes.

## Author

Developed by **Arfian**.

