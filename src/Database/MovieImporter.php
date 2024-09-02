<?php

class MovieImporter
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function createTable()
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS importedmovies (
                id INT AUTO_INCREMENT PRIMARY KEY,
                movieName VARCHAR(255) NOT NULL,
                releaseYear INT NOT NULL,
                genre VARCHAR(255) NOT NULL,
                description TEXT,
                orderIndex INT NOT NULL,
                UNIQUE KEY unique_movie_order (orderIndex)
            )
        ");
    }

    public function importFromCSV($csvFilePath)
    {
        if (!file_exists($csvFilePath)) {
            throw new Exception("Fișierul CSV nu există: $csvFilePath");
        }

        // Verifică dacă tabelul există deja în baza de date
        if (!$this->tableExists()) {
            // Dacă tabelul nu există, creează-l
            $this->createTable();
        }

        // Citeste datele din CSV
        $data = array_map('str_getcsv', file($csvFilePath));

        // Elimina antetul
        array_shift($data);

        // Sortează datele în funcție de ordine (Film 1, Film 2, etc.)
        usort($data, function ($a, $b) {
            return strnatcmp($a[0], $b[0]);
        });

        // Inserează datele în tabel
        $this->insertData($data);
    }

    public function displayMovies()
    {
        // Selectează toate filmele din tabel
        $statement = $this->pdo->prepare("SELECT * FROM importedmovies ORDER BY orderIndex");
        $statement->execute();
        $movies = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Afisează filmele
        foreach ($movies as $movie) {
            echo "<div class='movie-item' data-genre='{$movie['genre']}'>";
            echo "<h2>{$movie['movieName']}</h2>";
            echo "<p>An de apariție: {$movie['releaseYear']}</p>";
            echo "<p>Gen: {$movie['genre']}</p>";
            echo "<p>Descriere: {$movie['description']}</p>";
            echo "</div>";
        }
    }

    private function insertData($data)
    {
        $statement = $this->pdo->prepare("
            INSERT INTO importedmovies (movieName, releaseYear, genre, description, orderIndex)
            VALUES (:movieName, :releaseYear, :genre, :description, :orderIndex)
        ");

        $orderIndex = 1;

        foreach ($data as $row) {
            $statement->execute([
                ':movieName' => $row[0],
                ':releaseYear' => $row[1],
                ':genre' => $row[2],
                ':description' => $row[3],
                ':orderIndex' => $orderIndex,
            ]);

            $orderIndex++;
        }
    }

    public function tableExists()
    {
        $statement = $this->pdo->query("SHOW TABLES LIKE 'importedMovies'");
        return $statement->rowCount() > 0;
    }
}
