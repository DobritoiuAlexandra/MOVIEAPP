<?php
require_once('../src/Utils/includes.php');
require_once('../src/Utils/Auth.php');

use Src\Utils\Auth;

// Încarcă fișierul de configurare și inițializează obiectul PDO
require_once('../src/Database/database.php');
// Inițializează clasa Auth cu obiectul PDO
$pdo = Database::getInstance();
Auth::init($pdo);

// Verifică dacă utilizatorul este deja autentificat și îl redirecționează către pagina principală
redirectIfLoggedIn();

// Verifică dacă s-a trimis un formular de autentificare
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username'], $_POST['password'])) {
        // Filtrare și validare input-uri
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

        // Verifică autentificarea
        if (Auth::login($username, $password)) {
            // Autentificare reușită, setează sesiunea și redirecționează către pagina principală
            $_SESSION['user_id'] = Auth::getUserId($username);
            $_SESSION['username'] = $username;
            header('Location: index.php');
            exit();
        } else {
            // Autentificare eșuată, furnizează un mesaj de eroare
            $error_message = 'Autentificare eșuată. Verificați numele de utilizator sau parola.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/login-register-logout.css">
    <link rel="stylesheet" href="../assets/css/footer-style.css">
</head>
<body>
    <div class="container">
        <?php if (isset($error_message)) : ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <label for="username">Nume de utilizator:</label>
            <input type="text" id="username" name="username" required autocomplete="username">

            <label for="password">Parolă:</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">

            <button type="submit">Autentificare</button>
        </form>

        <p>Nu ai un cont? <a href="register.php">Înregistrează-te aici</a>.</p>
    </div>

    <?php include('../Teamplates/footer.php'); ?>
</body>
</html>

