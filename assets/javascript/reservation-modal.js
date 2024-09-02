var reservedSeats = [];
var movieDetailsTitle;

const menuOpenBtn = document.querySelector('[data-menu-open-btn]');
const menuCloseBtn = document.querySelector('[data-menu-close-btn]');
const navbar = document.querySelector('[data-navbar]');

menuOpenBtn.addEventListener('click', () => {
  navbar.classList.add('active');
  document.body.style.overflow = 'hidden'; // Blochează scrolling-ul când meniul este activ
});

menuCloseBtn.addEventListener('click', () => {
  navbar.classList.remove('active');
  document.body.style.overflow = ''; // Activează scrolling-ul când meniul este închis
});

// Funcția pentru a obține detaliile filmului de la server
function getMovieDetailsFromServer(movieTitle) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'Endpoints/MovieDetailsEndpoint.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('movieTitle=' + encodeURIComponent(sanitizeMovieName(movieTitle)));

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var movieDetails = JSON.parse(xhr.responseText);
            generateSeatContainer(movieDetails); // Apelați refreshMovieDetails aici
        }
    };
}

function createXHR() {
    return new XMLHttpRequest();
}

function reserveSeat() {
    // Trimite lista scaunelor rezervate la server
    updateReservedSeats(movieDetailsTitle, reservedSeats);
}

function updateReservedSeats(movieTitle, reservedSeats) {
    var xhr = createXHR(); // Inițializare XHR local
    if (xhr) {
        xhr.open('POST', 'Endpoints/UpdateSeatsEndpoint.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
        
        var data = {
            movieTitle: movieTitle,
            reservedSeats: reservedSeats
        };

        xhr.send(JSON.stringify(data));

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);

                    if (response.success) {
                        // Afișează mesajul de succes
                        var successMessage = document.getElementById('successMessage');
                        successMessage.textContent = response.message;
                        successMessage.style.display = 'block';

                        // Ascunde mesajul de eroare (dacă există)
                        var errorMessage = document.getElementById('errorMessage');
                        errorMessage.style.display = 'none';

                        // Actualizează detaliile filmului după succes
                        getMovieDetailsFromServer(movieTitle);
                    } else {
                        // Afișează mesajul de eroare
                        var errorMessage = document.getElementById('errorMessage');
                        errorMessage.textContent = response.error;
                        errorMessage.style.display = 'block';

                        // Ascunde mesajul de succes (dacă există)
                        var successMessage = document.getElementById('successMessage');
                        successMessage.style.display = 'none';

                        console.error('Eroare la actualizarea locurilor:', response.error);
                    }
                } else {
                    // Afișează mesajul de eroare pentru codul de stare
                    var errorMessage = document.getElementById('errorMessage');
                    errorMessage.textContent = 'Eroare la comunicarea cu serverul. Cod de stare: ' + xhr.status;
                    errorMessage.style.display = 'block';

                    // Ascunde mesajul de succes (dacă există)
                    var successMessage = document.getElementById('successMessage');
                    successMessage.style.display = 'none';

                    console.error('Eroare la comunicarea cu serverul. Cod de stare:', xhr.status);
                }
            }
        };
    } else {
        console.error('xhr is not defined');
    }
}

// Funcție pentru a verifica dacă scaunul este disponibil
function isSeatAvailable(row, seat, movieDetails) {
    // Construiește numele scaunului în conformitate cu formatul din detaliile filmului
    var seatName = 'row' + row + 'seat' + seat;

    // Verifică dacă scaunul este ocupat
    var isOccupied = movieDetails[seatName] === '1';

    // Returnează true dacă scaunul nu este ocupat, altfel returnează false
    return !isOccupied;
}

// Funcție pentru rezervarea scaunului
function selectPosibleReserveSeat(row, seat, movieDetails) {
    var seatName = 'row' + row + 'seat' + seat;
    var seatElement = document.querySelector(`.seatContainer .seatRow:nth-child(${row}) .seat:nth-child(${seat + 1})`);

    var isReserved = reservedSeats.some(reservedSeat => reservedSeat.row === row && reservedSeat.seat === seat);
    var isAvailable = !movieDetails[seatName] || movieDetails[seatName] === '0';

    if (isReserved) {
        // Scaunul este deja rezervat, faceți-l disponibil și eliminați-l din lista reservedSeats
        seatElement.style.backgroundImage = 'url(../assets/images/SeatAvailable.png)';
        reservedSeats = reservedSeats.filter(reservedSeat => !(reservedSeat.row === row && reservedSeat.seat === seat));
        movieDetailsTitle = movieDetails.MovieName;
    } else if (isAvailable) {
        // Scaunul este disponibil, faceți-l rezervat și adăugați-l în lista reservedSeats
        seatElement.style.backgroundImage = 'url(../assets/images/SeatSelected.png)';
        reservedSeats.push({ row: row, seat: seat });
        movieDetailsTitle = movieDetails.MovieName;
    } else {
        console.log('Scaunul nu este disponibil pentru rezervare.');
    }
}


function sanitizeMovieName(movieName) {
    // Înlocuiește caracterele nepermise cu caracterul underscore
    return movieName.replace(/[^a-zA-Z0-9_]/g, '_');
}

function generateSeatContainer(movieDetails) {
    var seatContainer = document.getElementById('seatContainer');
    seatContainer.innerHTML = ''; // Curăță conținutul existent

    for (var i = 1; i <= 10; i++) {
        var seatRow = document.createElement('div');
        seatRow.classList.add('seatRow');

        var rowNumber = document.createElement('div');
        rowNumber.classList.add('rowNumber');
        rowNumber.textContent = i;
        seatRow.appendChild(rowNumber);

        for (var j = 1; j <= 12; j++) {
            var seatName = 'row' + i + 'seat' + j;

            // Obține detalii despre scaun din obiectul movieDetails
            var isOccupied = movieDetails[seatName] === '1';

            var seat = document.createElement('div');
            seat.classList.add('seat');
            seat.classList.add(isOccupied ? 'occupied' : 'available');
            seat.style.backgroundImage = 'url(../assets/images/' + (isOccupied ? 'SeatUnavailable.png' : 'SeatAvailable.png') + ')';
            seat.textContent = j;

            // Adaugă evenimentul de click pentru rezervarea scaunului
            seat.onclick = function(row, col) {
                return function() {
                    selectPosibleReserveSeat(row, col, movieDetails);
                };
            }(i, j);

            seatRow.appendChild(seat);
        }

        seatContainer.appendChild(seatRow);
    }
}

// Funcție pentru deschiderea modalului de rezervare
function openReservationModal(title) {

    var modal = document.getElementById('reservationModal');
    modal.style.display = 'block';

    var modalTitle = document.querySelector('.reservation-modal-text h2');
    modalTitle.textContent = `Cumpara acum bilete la: ` + title;

    var xhr = createXHR(); // Inițializare XHR local

    // Modifică cererea să fie de tip POST și să trimită datele în corpul cererii
    var movieDetailsEndpointPath = 'Endpoints/MovieDetailsEndpoint.php';
    xhr.open('POST', movieDetailsEndpointPath, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('movieTitle=' + encodeURIComponent(sanitizeMovieName(title)));

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Răspunsul conține detaliile filmului sub formă de JSON
            var movieDetails = JSON.parse(xhr.responseText);
            // Apelul funcției pentru a genera containerul de scaune
            generateSeatContainer(movieDetails);
        }
    };

    disableScroll();
}


// Funcție pentru închiderea modalului de rezervare
function closeReservationModal() {
    // Inițializează movieDetailsTitle cu o valoare goală
    movieDetailsTitle = "";

    var modal = document.getElementById('reservationModal');
    modal.style.display = 'none';

    enableScroll();
}

// Adăugați acest eveniment pentru a închide modalul când se apasă pe fundal (zona din afara modalului)
window.addEventListener('click', function(event) {
    var modal = document.getElementById('reservationModal');
    if (event.target === modal) {
        closeReservationModal();
    }
});

// Adăugați acest eveniment pentru a închide modalul când se apasă tasta Esc
document.addEventListener('keydown', function(event) {
    var modal = document.getElementById('reservationModal');
    if (event.key === 'Escape' && modal.style.display !== 'none') {
        closeReservationModal();
    }
});


// Funcție pentru dezactivarea scroll-ului
function disableScroll() {
    document.documentElement.style.overflow = 'hidden';
}

// Funcție pentru activarea scroll-ului
function enableScroll() {
    document.documentElement.style.overflow = 'auto';
}
