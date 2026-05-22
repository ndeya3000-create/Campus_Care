<?php
require_once 'database.php';

// ── Vérifier que la requête vient bien du formulaire ──────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// ── Récupérer et nettoyer les données du formulaire ───────────
$full_name = trim($_POST['fullname']   ?? '');
$email     = trim($_POST['email']      ?? '');
$password  =      $_POST['password']   ?? '';

// Générer le username automatiquement depuis le nom complet
// Ex: "Mamadou Diallo" → "mamadou.diallo"
$username  = strtolower(str_replace(' ', '.', $full_name));

// ── Validation des données ────────────────────────────────────
$errors = [];

if (empty($full_name)) {
    $errors[] = "Le nom complet est obligatoire.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "L'adresse email est invalide.";
}

if (strlen($password) < 8) {
    $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
}

// ── Vérifier si l'email existe déjà ──────────────────────────
if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $errors[] = "Cet email est déjà utilisé. <a href='login.php'>Se connecter</a>";
    }
}

// ── Vérifier si le username existe déjà ──────────────────────
if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->fetch()) {
        // Ajouter un numéro aléatoire pour rendre le username unique
        $username .= rand(10, 99);
    }
}

// ── S'il y a des erreurs, retourner sur le formulaire ─────────
if (!empty($errors)) {
    $error_message = urlencode(implode('|', $errors));
    header("Location: login.php?register_error=$error_message");
    exit;
}

// ── Hacher le mot de passe (sécurité) ────────────────────────
$password_hash = password_hash($password, PASSWORD_BCRYPT);

// ── Insérer l'utilisateur dans la base de données ────────────
try {
    $stmt = $pdo->prepare("
        INSERT INTO users (username, full_name, email, password_hash, role, auth_provider)
        VALUES (?, ?, ?, ?, 'etudiant', 'local')
    ");

    $stmt->execute([$username, $full_name, $email, $password_hash]);

    // ── Succès : rediriger vers login avec message ────────────
    header("Location: login.php?success=1&username=" . urlencode($username));
    exit;

} catch (PDOException $e) {
    error_log("Erreur inscription : " . $e->getMessage());
    header("Location: login.php?register_error=" . urlencode("Une erreur est survenue. Réessayez."));
    exit;
}
?>