<?php

// ── Configuration de la base de données ───────────────────────
define('DB_HOST',    'localhost');
define('DB_NAME',    'Campus_Care');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

// ── Connexion PDO ──────────────────────────────────────────────
function getConnection(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST
             . ";dbname="    . DB_NAME
             . ";charset="   . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // Lève des exceptions sur erreur
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // Retourne des tableaux associatifs
            PDO::ATTR_EMULATE_PREPARES   => false,                    // Requêtes préparées natives (sécurité)
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",      // Support des emojis et caractères spéciaux
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // En production : logguer l'erreur, ne pas l'afficher
            error_log("Erreur DB : " . $e->getMessage());
            die(json_encode([
                'success' => false,
                'message' => "Service temporairement indisponible. Veuillez réessayer."
            ]));
        }
    }

    return $pdo;
}

// ── Instance globale ───────────────────────────────────────────
$pdo = getConnection();