<?php
// src/utils/Auth.php

namespace Src\Utils;

use PDO;
use PDOException;


class Auth
{
    private static $pdo;

    // Metoda pentru inițializarea obiectului PDO
    public static function init(PDO $pdo)
    {
        self::$pdo = $pdo;
    }

    // Metoda pentru verificarea credențialelor utilizatorului
    public static function login($username, $password): bool
    {
        try {
            // Verifică dacă utilizatorul există în baza de date și parola este corectă
            $stmt = self::$pdo->prepare('SELECT * FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            //dbg mode, i do not have admin password hashed yet
            if ($username == "admin" && $password == "admin") return true;
            
            if ($user && password_verify($password, $user['password']))
            {
                return true;
            }
        } catch (PDOException $e) {
            // Tratează excepția (afișează un mesaj, înregistrează etc.)
            echo 'Eroare la autentificare: ' . $e->getMessage();
        }

        return false;
    }

    /**
     * Înregistrează un nou utilizator în sistem.
     *
     * @param string $username Numele de utilizator al utilizatorului nou.
     * @param string $password Parola utilizatorului nou.
     *
     * @return bool Returnează true dacă înregistrarea a avut succes, altfel false.
     */
    public static function register(string $username, string $password): bool
    {
        try {
            // Verifică dacă utilizatorul există deja în baza de date
            $stmt = self::$pdo->prepare('SELECT * FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingUser) {
                // Utilizatorul există deja, deci nu se poate face înregistrarea
                return false;
            }

            // Hash-ează parola utilizând algoritmul implicit
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Adaugă utilizatorul în baza de date
            $stmt = self::$pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            $stmt->execute([$username, $hashedPassword]);

            return true; // Înregistrarea a avut succes
        } catch (PDOException $e) {
            // Tratează excepția (afișează un mesaj, înregistrează etc.)
            echo 'Eroare la înregistrare: ' . $e->getMessage();
        }

        return false; // Înregistrarea a eșuat din cauza unei excepții
    }

    // Metoda pentru obținerea ID-ului utilizatorului după numele de utilizator
    public static function getUserId($username)
    {
        try {
            // Obține ID-ul utilizatorului după numele de utilizator
            $stmt = self::$pdo->prepare('SELECT id FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user ? $user['id'] : null;
        } catch (PDOException $e) {
            // Tratează excepția (afișează un mesaj, înregistrează etc.)
            echo 'Eroare la obținerea ID-ului utilizatorului: ' . $e->getMessage();
        }

        return null;
    }
}

