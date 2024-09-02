<?php

namespace Src\Database;

use Exception;
use PDO;
use PDOException;

class MovieDatabaseManager
{
    private static $pdo;
    const TABLE_NAME = 'MoviesList';
    const TOTAL_ROWS = 10;
    const SEATS_PER_ROW = 12;

    public static function init(PDO $pdo)
    {
        self::$pdo = $pdo;
    }

    // Funcție pentru a verifica dacă tabela MoviesList există
    private static function checkMoviesListTable()
    {
        try {
            if (!self::tableExists()) {
                self::createMoviesListTable();
            }
        } catch (PDOException $e) {
            // Handle the exception (display a message, log, etc.)
            echo 'Error checking MoviesList table: ' . $e->getMessage();
        }
    }

    // Funcție pentru a crea tabela MoviesList
    private static function createMoviesListTable()
    {
        try {
            $sql = "CREATE TABLE " . self::TABLE_NAME . " (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                MovieName VARCHAR(255) NOT NULL,
                " . self::generateSeatColumns() . "
            )";

            self::$pdo->exec($sql);
        } catch (PDOException $e) {
            // Handle the exception (display a message, log, etc.)
            echo 'Error creating the MoviesList table: ' . $e->getMessage();
        }
    }

    // Funcție pentru a adăuga un film în tabela MoviesList
    public static function addMovieToMoviesList($movieName)
    {
        try {
            // Verifică și creează tabela MoviesList dacă nu există
            self::checkMoviesListTable();

            // Sanitize the movie name before insertion
            $sanitizedMovieName = self::sanitizeMovieName($movieName);

            // Check if the movie already exists in MoviesList
            if (!self::movieExistsInMoviesList($sanitizedMovieName)) {
                // Now, insert the sanitized movie name into the table
                $insertSql = "INSERT INTO " . self::TABLE_NAME . " (MovieName) VALUES (:movieName)";
                $stmt = self::$pdo->prepare($insertSql);
                $stmt->bindParam(':movieName', $sanitizedMovieName, PDO::PARAM_STR);
                $stmt->execute();

                // Populate the movie table with random seat data
                self::populateMovieName($sanitizedMovieName);
            }
        } catch (PDOException $e) {
            // Handle the exception (display a message, log, etc.)
            echo 'Error adding movie to MoviesList: ' . $e->getMessage();
        }
    }

    // Funcție auxiliară pentru a verifica dacă un film există în tabela MoviesList
    private static function movieExistsInMoviesList($movieName): bool
    {
        $sanitizedMovieName = self::sanitizeMovieName($movieName);

        $sql = "SELECT id FROM " . self::TABLE_NAME . " WHERE MovieName = :movieName";
        $stmt = self::$pdo->prepare($sql);
        $stmt->bindParam(':movieName', $sanitizedMovieName, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

// Funcție auxiliară pentru a verifica dacă tabela MoviesList există
    private static function tableExists(): bool
    {
        try {
            $tableName = self::TABLE_NAME;  // Salvează numele tabelului într-o variabilă
            $stmt = self::$pdo->prepare("SHOW TABLES LIKE :tableName");
            $stmt->bindParam(':tableName', $tableName, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 255);  // Adaugă flag-ul PARAM_INPUT_OUTPUT și specifică lungimea
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            // Handle the exception (display a message, log, etc.)
            echo 'Error checking table existence: ' . $e->getMessage();
            return false;  // Returnează false în caz de eroare
        }
    }

    // Funcție auxiliară pentru a genera coloanele pentru locuri
    private static function generateSeatColumns(): string
    {
        $columns = [];
        for ($row = 1; $row <= self::TOTAL_ROWS; $row++) {
            for ($seat = 1; $seat <= self::SEATS_PER_ROW; $seat++) {
                $columns[] = "row{$row}seat$seat BOOLEAN NOT NULL DEFAULT TRUE";
            }
        }

        return implode(", ", $columns);
    }

    // Funcție auxiliară pentru a popula inițial tabela cu locuri pentru un film
    private static function populateMovieName($movieName)
    {
        try {
            // Check if the table already exists
            if (self::tableExists()) {
                // Populate the table with random seat data for the specified movie
                for ($row = 1; $row <= self::TOTAL_ROWS; $row++) {
                    for ($seat = 1; $seat <= self::SEATS_PER_ROW; $seat++) {
                        $columnName = "row{$row}seat$seat";
                        $isOccupied = (rand(0, 1) == 0) ? 1 : 0; // Randomizează disponibilitatea locurilor

                        $updateSql = "UPDATE " . self::TABLE_NAME . " SET $columnName = $isOccupied WHERE MovieName = :movieName";
                        $stmt = self::$pdo->prepare($updateSql);
                        $stmt->bindParam(':movieName', $movieName, PDO::PARAM_STR);
                        $stmt->execute();
                    }
                }
            } else {
                echo 'Table does not exist: ' . self::TABLE_NAME;
            }
        } catch (PDOException $e) {
            // Handle the exception (display a message, log, etc.)
            echo 'Error populating the table: ' . $e->getMessage();
        }
    }

    // Funcție auxiliară pentru a preveni SQL injection
    private static function sanitizeMovieName($movieName)
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '_', $movieName);
    }

    public static function clearMoviesListTable()
    {
        try {
            // Check if the table already exists
            if (self::tableExists()) {
                // Delete all rows from the table and reset auto-increment ID
                $truncateSql = "TRUNCATE TABLE " . self::TABLE_NAME;
                self::$pdo->exec($truncateSql);
            } else {
                echo 'Table does not exist: ' . self::TABLE_NAME;
            }
        } catch (PDOException $e) {
            // Handle the exception (display a message, log, etc.)
            echo 'Error clearing the table: ' . $e->getMessage();
        }
    }

    public static function getMovieDetails(string $movieName): ?array
    {
        try {
            $tableName = self::TABLE_NAME;
            $sql = "SELECT * FROM $tableName WHERE MovieName = :movieName";
            $stmt = self::$pdo->prepare($sql);
            $stmt->bindParam(':movieName', $movieName, PDO::PARAM_STR);
            $stmt->execute();

            // Verificare dacă există un rând în rezultate
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($result !== false) ? $result : null;
        } catch (PDOException $e) {
            throw new Exception('Error getting movie details: ' . $e->getMessage());
        }
    }

    public static function updateAvalibleSeatsToReservedSeats($movieTitle, $reservedSeats)
    {
        try {
            $pdo = self::$pdo;
    
            foreach ($reservedSeats as $seat) {
                $row = $seat['row'];
                $seatNumber = $seat['seat'];
    
                // Format the seat name as "rowXseatY"
                $seatName = "row{$row}seat{$seatNumber}";
    
                // Update the seat status in the database to 1 (occupied)
                $updateSql = "UPDATE " . self::TABLE_NAME . " SET $seatName = 1 WHERE MovieName = :movieTitle";
                $stmt = $pdo->prepare($updateSql);
                $stmt->bindParam(':movieTitle', $movieTitle, PDO::PARAM_STR);
                $stmt->execute();
            }
    
            return true;
        } catch (Exception $e) {
            // Aruncă o excepție sau gestionează eroarea în funcție de necesități
            error_log($e->getMessage());
            return false;
        }
    }
    




}
