<?php
// logout.php

require_once('../src/Security/session.php');

$loggedOutUsername = logout();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link rel="stylesheet" href="../assets/css/login-register-logout.css">
    <link rel="stylesheet" href="../assets/css/footer-style.css">
</head>
<body>
<div class="container">
    <h2>Deconectare reușită</h2>
    <?php if ($loggedOutUsername) : ?>
        <!-- Afișează un mesaj personalizat dacă există un nume de utilizator -->
        <p>Ați fost deconectat, <?php echo htmlspecialchars($loggedOutUsername); ?>!</p>
    <?php else : ?>
        <!-- Afișează un mesaj generic dacă nu există un nume de utilizator -->
        <p>Ați fost deconectat!</p>
    <?php endif; ?>
    <p><a class="link" href="login.php">Autentificare</a></p>
</div>
</body>
</html>
