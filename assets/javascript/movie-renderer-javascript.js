// Adaugă un eveniment de clic pentru fiecare element cu clasa "movie-overview"
document.querySelectorAll('.movie-overview').forEach(function(overview) {
    // Salvează textul complet în atributul data-full-text
    var fullText = overview.textContent.trim();
    overview.setAttribute('data-full-text', fullText);

    // Setează textul trunchiat inițial
    overview.textContent = truncateText(fullText, 10);

    // Adaugă evenimentul de clic
    overview.addEventListener('click', function() {
        // Afișează sau ascunde restul textului la clic
        toggleOverviewText(overview);
    });
});

// Funcția care va gestiona afișarea/ascunderea textului
function toggleOverviewText(overviewElement) {
    // Obține textul complet și textul trunchiat original
    var fullText = overviewElement.getAttribute('data-full-text') || '';
    var truncatedText = truncateText(fullText, 10);

    // Schimbă între textul complet și textul trunchiat
    if (overviewElement.textContent === fullText) {
        overviewElement.textContent = truncatedText;
    } else {
        overviewElement.textContent = fullText;
    }
}

// Funcție pentru a trunchia textul la un număr dat de cuvinte
function truncateText(text, wordCount) {
    if (text) {
        var words = text.split(/\s+/);
        if (words.length > wordCount) {
            return words.slice(0, wordCount).join(' ') + '...';
        } else {
            return text;
        }
    } else {
        return '';
    }
}
