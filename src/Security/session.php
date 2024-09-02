<?php

session_start();

function isUserLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function requireLogin()
{
    if (!isUserLoggedIn()) {
        header('Location: login.php'); // Redirecționează către pagina de login
        exit;
    }
}

function redirectIfLoggedIn()
{
    if (isUserLoggedIn()) {
        header('Location: index.php'); // Redirecționează către pagina principală
        exit;
    }
}

function logout()
{
    // Salvăm numele utilizatorului înainte de a distruge sesiunea
    $loggedOutUsername = $_SESSION['username'] ?? null;

    // Dezactivează și distruge sesiunea
    $_SESSION = array(); // Golește array-ul $_SESSION pentru a șterge toate datele de sesiune
    unset($_SESSION['username']); // Dezactivează specific cheia 'username' din sesiune
    unset($_SESSION['user_id']); // Dezactivează specific cheia 'user_id' din sesiune
    session_destroy(); // Distrugerea sesiunii

    return $loggedOutUsername;
}