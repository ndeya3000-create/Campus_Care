<?php
session_start();
require_once 'database.php';

// ── Vérifier que la requête vient bien du formulaire ──────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// ── Récupérer les données du formulaire ───────────────────────
$username = trim($_POST['username'] ?? '');
$password =      $_POST['password'] ?? '';

// ── Vérifier que les champs ne sont pas vides ─────────────────
if (empty($username) || empty($password)) {
    header('Location: login.php?error=1');
    exit;
}

// ── Chercher l'utilisateur dans la base de données ───────────
try {
    $stmt = $pdo->prepare("
        SELECT id, username, full_name, email, password_hash, role, is_active
        FROM users
        WHERE username = ?
        LIMIT 1
    ");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

} catch (PDOException $e) {
    error_log("Erreur connexion : " . $e->getMessage());
    header('Location: login.php?error=1');
    exit;
}

// ── Vérifier si l'utilisateur existe ─────────────────────────
if (!$user) {
    header('Location: login.php?error=1');
    exit;
}

// ── Vérifier si le compte est actif ──────────────────────────
if (!$user['is_active']) {
    header('Location: login.php?error=disabled');
    exit;
}

// ── Vérifier le mot de passe ──────────────────────────────────
if (!password_verify($password, $user['password_hash'])) {
    header('Location: login.php?error=1');
    exit;
}

// ── Connexion réussie : créer la session ──────────────────────
$_SESSION['user_id']   = $user['id'];
$_SESSION['username']  = $user['username'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['email']     = $user['email'];
$_SESSION['role']      = $user['role'];

// ── Mettre à jour la date de dernière connexion ───────────────
$stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
$stmt->execute([$user['id']]);

// ── Rediriger selon le rôle ───────────────────────────────────
switch ($user['role']) {
    case 'admin':
        header('Location: admin/dashboard.php');
        break;
    case 'medecin':
        header('Location: medecin/dashboard.php');
        break;
    case 'personnel':
        header('Location: personnel/dashboard.php');
        break;
    default: // etudiant
        header('Location: etudiant/dashboard.php');
        break;
}
exit;
?>
