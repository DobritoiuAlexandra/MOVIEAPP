<?php

namespace Src\Api;

require_once(__DIR__ . '/../Database/MovieDatabaseManager.php');
require_once(__DIR__ . '/../Database/database.php');

use Database;
use Exception;
use Src\Database\MovieDatabaseManager;

try {
    $pdo = Database::getInstance();
    MovieDatabaseManager::init($pdo);
} catch (Exception $e) {
    echo 'A apărut o eroare la inițializarea bazei de date: ' . $e->getMessage();
}


class MovieRenderer
{
    public static function displayMovieInfo($result, $imageType)
    {
        // Verifică dacă filmul există deja în baza de date și adaugă-l dacă nu există
        MovieDatabaseManager::addMovieToMoviesList($result->title);
        // MovieDatabaseManager::clearMoviesListTable();

        // Afișarea imaginii și informațiilor despre film
        $escapedTitle = htmlspecialchars($result->title ?? 'N/A');
        $buttonText = 'Cumpără bilet la ' . $escapedTitle;

        echo '<div class="movie-item">';
        echo '<img class="movie-image" src="' . MovieRenderer::getImage($result, $imageType) . '" alt="Movie name: ' . $escapedTitle . '">';

        // Container pentru titlu și dată de lansare
        echo '<div class="movie-details">';
        echo '<div class="movie-title">' . $escapedTitle . '</div>';
        echo '<div class="movie-release-date">' . ($result->release_date ?? 'N/A') . '</div>';
        echo '</div>';

        // Afișarea descrierii
        echo '<div class="movie-overview-container">';
        echo '<div class="movie-overview">' . ($result->overview ?? 'N/A') . '</div>';
        echo '</div>';

        // Afișarea butonului de cumpărare a biletului
        echo '<button class="buy-button" data-movie-title="' . $escapedTitle . '" onclick="openReservationModal(\'' . $escapedTitle . '\')">';
        echo $buttonText;
        echo '</button>';

        echo '</div>';
    }



    public static function getImage($result, $imageType): string
    {
        $imagePath = $result->poster_path ?? $result->backdrop_path;

        // Verifică dacă $imagePath este nenul și este un șir de caractere
        if ($imagePath && is_string($imagePath)) {
            return "https://image.tmdb.org/t/p/$imageType/$imagePath";
        } else {
            // Returnează URL-ul pentru imaginea default
            return "https://upload.wikimedia.org/wikipedia/commons/6/65/No-Image-Placeholder.svg";
        }
    }
}

