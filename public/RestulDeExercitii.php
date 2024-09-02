<!-- Deschideți documentul HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Setările meta și legătura către fișierele CSS și iconița -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/images/favicon.svg" type="image/x-icon">
    <title>Tema Dobritoiu Alexandra</title>
    <link rel="stylesheet" href="../assets/css/RestulDeExercitii.css">
</head>
<body>

<?php
// Includerea fișierelor necesare PHP
require_once('../src/Utils/includes.php');
require_once('../src/Database/database.php');
require_once('../src/Database/MovieImporter.php');

try {
    // Obținerea unei instanțe a bazei de date și a importerului de filme
    $pdo = Database::getInstance();
    $movieImporter = new MovieImporter($pdo);

    // Importul datelor din CSV în baza de date
    if (!$movieImporter->tableExists()) {
        $movieImporter->importFromCSV(__DIR__ . '/Files/movies.csv');
    }
} catch (Exception $e) {
    // Gestionarea erorilor în cazul în care apar probleme
    echo 'A apărut o eroare la importul datelor: ' . $e->getMessage();
}
?>

<!-- Tabel pentru structura generală a paginii -->
<table class="layout-table">
    <!-- Rând pentru antetul paginii -->
    <tr>
        <td colspan="2" class="header">
            <h1>Restul de exercitii</h1>
        </td>
    </tr>
    <!-- Rând pentru bara de navigare -->
    <tr>
        <td colspan="2" class="nav">
            <ul>
                <!-- Link către pagina principală -->
                <li><a href="./index.php" class="navbar-link">Home</a></li>
                <!-- Meniu derulant pentru genuri -->
                <li class="dropdown">
                    <a href="#" class="dropbtn">Genres</a>
                    <!-- Container pentru genuri, va fi completat dinamic cu JavaScript -->
                    <div class="dropdown-content" id="genreDropdown">
                        <!-- Genurile vor fi încărcate aici folosind JavaScript -->
                    </div>
                </li>
            </ul>
        </td>
    </tr>
    <!-- Rând pentru conținutul exercițiilor -->
    <tr>
        <td colspan="2" class="ex-container">
            <!-- Paragraf cu text centrat și alb -->
            <p id="statusText">Textul este centrat și alb.</p>
            <!-- Subtitlu pentru secțiunea CSS -->
            <h2>CSS:</h2>
            <!-- Puncte specifice pentru stilizarea folosind CSS -->
            <p>stilizarea să fie realizată în fișiere separate - ☑</p>
            <p>site-ul să fie responsive; (https://www.w3schools.com/html/html_responsive.asp) - ☑</p>
            <p>layout-ul să fie realizat cu ajutorul tabelelor; - ☑</p>
            <p>să conțină un menu drop-down realizat cu CSS; - ☑</p>
            <p>să se folosească transformări CSS; - ☑</p>
            <!-- Subtitlu pentru secțiunea JavaScript/jQuery -->
            <h2>Elemente JavaScript/jQuery:</h2>
            <!-- Puncte specifice pentru JavaScript/jQuery -->
            <p>modificarea stilului unui element sau al unui grup de elemente; - ☑</p>
            <p>folosirea funcțiilor în validarea formularelor; - ☑</p>
            <p>folosirea evenimentelor de mouse și tastatură; - ☑</p>
            <p>modificare dinamică a poziției unui element; - ☑</p>
        </td>
    </tr>
    <!-- Rând pentru formularul de adăugare a filmului -->
    <tr>
        <td colspan="2" class="form-container">
            <h2>Adăugare film</h2>
            <!-- Formular pentru adăugarea unui film -->
            <form id="formularAdaugareFilm">
                <label for="nume">Nume:</label>
                <input type="text" id="nume" required>

                <label for="an_aparitie">An apariție:</label>
                <input type="number" id="an_aparitie" required>

                <label for="gen">Gen:</label>
                <select id="gen" name="gen">
                    <!-- Genurile vor fi încărcate aici folosind JavaScript -->
                </select>

                <label for="descriere">Descriere:</label>
                <textarea id="descriere" required></textarea>

                <!-- Buton pentru adăugarea filmului -->
                <button type="button" onclick="adaugaFilm()">Adaugă film</button>
            </form>
        </td>
    </tr>
    <!-- Rând pentru afișarea filmelor din baza de date -->
    <tr>
        <td colspan="2" class="movie-container">
            <?php
            // Afiseaza filmele din baza de date
            $movieImporter->displayMovies();
            ?>
        </td>
    </tr>
    <!-- Rând pentru partea de jos a paginii -->
    <tr>
        <td colspan="2" class="footer">
            <!-- Text de subsol cu anul curent și informații despre proiect -->
            <p>&copy; <?= date('Y'); ?> Acest proiect este creat de Dobritoiu Alexandra. Toate drepturile rezervate.</p>
        </td>
    </tr>
</table>

<!-- JavaScript pentru mutarea dinamică a elementului -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Exemplu de apel pentru a muta elementul după 2 secunde
        setTimeout(function () {
            moveElement();
        }, 2000);
    });

    // Funcția care modifică poziția elementului
    function moveElement() {
        const exContainer = document.querySelector('.ex-container');
        const statusText = document.getElementById('statusText');

        // Mută elementul cu 50px mai spre stânga
        exContainer.style.marginLeft = '50px';
        exContainer.style.color = 'red';

        // Actualizează textul în elementul <p>
        statusText.innerText = 'Textul a fost mutat din centru în stânga și făcut roșu. - (modificare dinamică a poziției unui element)';
    }
</script>

<!-- JavaScript pentru modificarea stilului unui element sau al unui grup de elemente -->
<script>
    let dropbtns = document.getElementsByClassName('dropbtn');

    // Iterează prin colecție și atașează evenimentele pentru fiecare element
    for (let i = 0; i < dropbtns.length; i++) {
        dropbtns[i].addEventListener('mouseover', function () {
            this.style.border = '2px solid #6C6C6CFF';
        });

        dropbtns[i].addEventListener('mouseout', function () {
            this.style.border = 'none';
        });
    }
</script>

<!-- JavaScript pentru încărcarea dinamică a genurilor -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Încarcă genurile din fișierul CSV
        fetch('Files/movies.csv')
            .then(response => response.text())
            .then(data => {
                const genres = extractGenres(data);
                const genreDropdown = document.getElementById('genreDropdown');
                const selectGen = document.getElementById('gen');

                genres.forEach(genre => {
                    // Adaugă opțiune în dropdown
                    const genreLink = document.createElement('a');
                    genreLink.href = '#';
                    genreLink.textContent = genre;
                    genreLink.addEventListener('click', () => filterMoviesByGenre(genre));
                    genreDropdown.appendChild(genreLink);

                    // Adaugă opțiune în select
                    const selectOption = document.createElement('option');
                    selectOption.value = genre;
                    selectOption.textContent = genre;
                    selectGen.appendChild(selectOption);
                });
            });
    });

    // Funcție pentru extragerea genurilor din datele CSV
    function extractGenres(csvData) {
        const lines = csvData.split('\n');
        const genres = new Set();

        for (let i = 1; i < lines.length; i++) {
            const columns = lines[i].split(',');
            if (columns.length >= 3) {
                genres.add(columns[2].trim());
            }
        }

        return Array.from(genres);
    }

    // Funcție pentru filtrarea filmelor după gen
    function filterMoviesByGenre(selectedGenre) {
        const movieContainer = document.querySelector('.movie-container');
        const movies = movieContainer.getElementsByClassName('movie-item');

        for (const movie of movies) {
            const movieGenre = movie.getAttribute('data-genre');

            if (selectedGenre === 'Toate' || selectedGenre === movieGenre) {
                movie.style.display = 'block';
            } else {
                movie.style.display = 'none';
            }
        }
    }

    // Funcție pentru adăugarea unui film
    function adaugaFilm() {
        // Ia valorile introduse de utilizator
        const nume = document.getElementById('nume').value;
        const anAparitie = document.getElementById('an_aparitie').value;
        const gen = document.getElementById('gen').value;
        const descriere = document.getElementById('descriere').value;

        // Validare simplă (puteți adăuga verificări mai complexe aici)
        if (!nume || !anAparitie || !gen || !descriere) {
            alert('Vă rugăm să completați toate câmpurile.');
            return;
        }

        // Creează un obiect cu datele noului film
        const filmNou = {
            nume: nume,
            an_aparitie: anAparitie,
            gen: gen,
            descriere: descriere
        };

        // Actualizează afișarea filmelor (sau orice altceva doriți să faceți cu datele)
        afiseazaFilm(filmNou);
    }

    // Funcție pentru afișarea unui film nou adăugat
    function afiseazaFilm(film) {
        // Creează un element pentru film
        const movieContainer = document.querySelector('.movie-container');
        const filmElement = document.createElement('div');
        filmElement.classList.add('movie-item');
        filmElement.setAttribute('data-genre', film.gen);

        // Creează conținutul filmului
        const filmContent = `
            <h3>${film.nume}</h3>
            <p>An apariție: ${film.an_aparitie}</p>
            <p>Gen: ${film.gen}</p>
            <p>${film.descriere}</p>
        `;

        // Adaugă conținutul la elementul filmului
        filmElement.innerHTML = filmContent;

        // Adaugă filmul la containerul general
        movieContainer.appendChild(filmElement);
    }
</script>

</body>
</html>
