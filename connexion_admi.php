<?php
$host   = "localhost";
$dbname = "centre_sante";
$user   = "root";
$pass   = "";  // ← chacun modifie sur SA machine sans push ce fichier

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["error" => $e->getMessage()]));
}
?>