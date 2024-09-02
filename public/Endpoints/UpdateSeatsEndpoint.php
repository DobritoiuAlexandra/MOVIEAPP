<?php
require_once('../../src/Database/MovieDatabaseManager.php');
require_once('../../src/Database/database.php');

use Src\Database\MovieDatabaseManager;

try {
    $pdo = Database::getInstance();
    MovieDatabaseManager::init($pdo);

    // Read raw JSON data from the request
    $jsonPayload = file_get_contents('php://input');

    // Decode JSON data
    $data = json_decode($jsonPayload, true);

    // Extract movieTitle and reservedSeats
    $movieTitle = $data['movieTitle'] ?? '';
    $reservedSeats = $data['reservedSeats'] ?? '';

    // Verifică dacă $reservedSeats nu este gol
    if (!empty($reservedSeats)) {
        // Print the values for debugging
        error_log('movieTitle: ' . $movieTitle);
        error_log('reservedSeats: ' . print_r($reservedSeats, true));

        // Verifică dacă $movieTitle nu este gol și $reservedSeats nu este un șir gol
        if (!empty($movieTitle) && !empty($reservedSeats)) {
            // Actualizează starea scaunelor rezervate în baza de date
            MovieDatabaseManager::updateAvalibleSeatsToReservedSeats($movieTitle, $reservedSeats);

            echo json_encode(['success' => true, 'message' => 'Locurile au fost cumpărate. Biletele pot fi găsite pe email.']);
        } else {
            echo json_encode(['error' => 'Invalid request', 'message' => 'Valorile celor doua variabile $movieTitle si $reservedSeats sunt empty.']);
        }
    } else {
        echo json_encode(['error' => 'Reserved seats data is empty']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'An unexpected error occurred.']);
    error_log($e->getMessage());
}
?>