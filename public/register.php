<!-- register.php -->

<?php
    require_once('../src/Utils/includes.php');
    require_once('../src/Utils/Auth.php');

    use Src\Utils\Auth;

    // Încarcă fișierul de configurare și inițializează obiectul PDO
    require_once('../src/Database/database.php');
    // Inițializează clasa Auth cu obiectul PDO
    $pdo = Database::getInstance();
    Auth::init($pdo);

    // Verifică dacă utilizatorul este deja autentificat; dacă da, redirecționează către pagina principală
    redirectIfLoggedIn();

    // Verifică dacă s-a trimis un formular de înregistrare
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

        // Încearcă să înregistreze utilizatorul
        if (Auth::register($username, $password)) {
            // Înregistrare reușită, redirecționează către pagina de login
            header('Location: login.php');
            exit();
        } else {
            // Înregistrare eșuată
            $error_message = 'Înregistrare eșuată. Verificați datele introduse.';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        <input type="text" id="username" name="username" required>

        <label for="password">Parolă:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Înregistrare</button>
    </form>

    <p>Deja ai un cont? <a href="login.php">Autentifică-te aici</a>.</p>
</div>
</body>
<?php include('../Teamplates/footer.php'); ?>
</html>
