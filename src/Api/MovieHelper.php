<?php
namespace Src\Api;

include_once('MovieRenderer.php');
    class MovieHelper
    {
        public static function renderForm($currentPage, $maxPages, $selectedType)
        {
            $selectedTypeContent = ucfirst($selectedType);

            echo <<<HTML
            <div class="container-form">
                <div class="form-description">
                    <p>Selected Type: $selectedTypeContent</p>
                </div>
                <div class="form-container">
                    <form method="GET" action="">
                        <div class="form-row">
                            <label for="page">Page:</label>
                            <input type="number" id="page" name="page" value="$currentPage" min="1" max="$maxPages">
                        </div>
                        <div class="form-row">
                            <label for="criteriaSelection">Criteria Selection:</label>
                            <select id="criteriaSelection" name="type">
            HTML;

            $types = ['popular', 'top_rated', 'upcoming'];
            foreach ($types as $type) {
                $selected = ($selectedType === $type) ? 'selected' : '';
                echo "<option value=\"$type\" $selected>" . ucfirst($type) . "</option>";
            }

            echo <<<HTML
                        </select>
                        </div>
                        <div class="button-container">
                            <button type="submit">Get Movies</button>
                        </div>
                    </form>
                </div>
            </div>
            HTML;
        }



        public static function displayMovies($movies, $imageType)
        {
            echo '<div class="movie-container">';
                foreach ($movies as $result) {
                    MovieRenderer::displayMovieInfo($result, $imageType);
                }
            echo '</div>';
        }
    }
