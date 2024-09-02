<?php
require_once('../../src/Database/MovieDatabaseManager.php');
require_once('../../src/Database/database.php');

use Src\Database\MovieDatabaseManager;

try {
    $pdo = Database::getInstance();
    MovieDatabaseManager::init($pdo);

    if (isset($_POST['movieTitle'])) {
        $movieTitle = $_POST['movieTitle'];

        // Obține detalii despre film
        $movieDetails = MovieDatabaseManager::getMovieDetails($movieTitle);

        header('Content-Type: application/json');
        echo json_encode($movieDetails);
    } else {
        // Dacă se face o altă tip de cerere (nu GET sau POST), poți arunca o excepție sau trata în alt mod
        echo 'Eroare: Titlul filmului nu a fost primit în solicitare.';
    }
} catch (Exception $e) {
    // Handle exceptions
    echo json_encode(['error' => 'An unexpected error occurred.']);
    error_log($e->getMessage());
}
?>
