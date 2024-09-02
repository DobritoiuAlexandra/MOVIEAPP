<?php
// index.php

require_once('../src/Utils/includes.php');
require_once('../src/Database/MovieDatabaseManager.php');

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Src\Enums\ImageType;
use Src\Api\MovieApiClient;
use Src\Api\MovieHelper;

requireLogin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tema Dobritoiu Alexandra</title>
    <link rel="icon" href="../assets/images/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/header-style.css">
    <link rel="stylesheet" href="../assets/css/reservation-modal.css">
    <link rel="stylesheet" href="../assets/css/movie-renderer-style.css">
    <link rel="stylesheet" href="../assets/css/render-form.css">
    <link rel="stylesheet" href="../assets/css/footer-style.css"> 
    <script defer src="../assets/javascript/reservation-modal.js"></script>
    <script defer src="../assets/javascript/movie-renderer-javascript.js"></script>
</head>
<body>

<header class="header" data-header>
    <button class="menu-open-btn" data-menu-open-btn>☰</button>
    <div class="container">
        <nav class="navbar" data-navbar>
            <ul class="navbar-list">
                <li><a href="./index.php" class="navbar-link">Home</a></li>
                <li><a href="./RestulDeExercitii.php" class="navbar-link">RestulDeExercitii</a></li>
            </ul>
            <div class="header-actions">
                <button class="close-btn" data-menu-close-btn>Closed button</button>
                <a class="btn btn-primary" href="logout.php">Deconectare</a>
            </div>
        </nav>
    </div>
</header>


<?php
    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $movieType = $_GET['type'] ?? 'popular';
    $imageType = ImageType::W600_AND_H900_BESTV2;

    $movieApiClient = new MovieApiClient();
    $url = $movieApiClient->buildUrl($movieType, $page);

    $config = require(__DIR__ . '/../src/Security/config.php');

    if (!is_array($config))
    {
        die('Error: Invalid configuration file.');
    }

    $api_key = $config['api_key'];

    MovieHelper::renderForm($page, 100, $movieType);

    try
    {
        $movies = $movieApiClient->getMovies($url, $api_key);
        if ($movies !== null)
        {
            MovieHelper::displayMovies($movies, $imageType);
        }
        else
        {
            echo "<p>Lista de filme lipsește sau nu este validă.</p>";
        }
    }
    catch (RequestException | GuzzleException $e)
    {
        echo "<p>Eroare la efectuarea request-ului: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
?>

<div id="reservationModal" class="reservation-modal" data-movie-title="<?php echo htmlspecialchars($title ?? ''); ?>">
    <div class="reservation-modal-content">
        <span class="reservation-modal-close-button" onclick="closeReservationModal()">&times;</span>
        <div id="successMessage" class="success-message"></div>
        <div id="errorMessage" class="error-message"></div>
        <div class="containerLegend">
            <div class="legendContainer">
                <div class="legend">
                    <img alt="" src="../assets/images/SeatAvailable.png" class="graphic available">
                    <span class="label">Disponibil</span>
                </div>
                <div class="legend">
                    <img alt="" src="../assets/images/SeatUnavailable.png" class="graphic available">
                    <span class="label">Indisponibil</span>
                </div>
                <div class="legend">
                    <img alt="" src="../assets/images/SeatSelected.png" class="graphic available">
                    <span class="label">Alegerea ta</span>
                </div>
            </div>
        </div>
        <div class="seatplanControl">
            <div class="imageContainer">
                <img src="../assets/images/screen.png" alt="Scaune de cinema">
            </div>
            <div class="seatContainer" id="seatContainer">
            </div>
        </div>
        <div class="reservation-modal-text">
            <h2></h2>
            <button class="reserve-button" onclick="reserveSeat()">Cumpara</button>
            <button class="close-button" onclick="closeReservationModal()">Închide</button>
        </div>
    </div>
</div>

<?php include('../Teamplates/footer.php'); ?>
</body>
</html>
