<?php
// ── Messages venant de register.php ──────────────────────────
$success_msg    = '';
$register_error = '';

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $username_created = htmlspecialchars($_GET['username'] ?? '');
    $success_msg = "Compte créé avec succès ! Votre identifiant : <strong>$username_created</strong>";
}

if (isset($_GET['register_error'])) {
    $register_error = htmlspecialchars(urldecode($_GET['register_error']));
}

$login_error = isset($_GET['error']) ? "Identifiants incorrects ou compte inexistant." : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Campus Care</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="bg-overlay"></div>
<div class="bg-grid"></div>

<div class="page-wrap">

    <div class="card">

        <!-- Logo + Nom -->
        <div class="brand">
            <div class="brand-icon">
                <i class="ti ti-heart-rate-monitor"></i>
            </div>
            <div class="brand-text">
                <h1>Campus Care</h1>
                <p>Centre de Santé · UASZ</p>
            </div>
        </div>

        <!-- Deux boutons principaux -->
        <div class="main-buttons">
            <button class="btn-login" onclick="showPanel('login')">
                <i class="ti ti-login"></i>
                Se connecter
            </button>
            <button class="btn-register" onclick="showPanel('register')">
                <i class="ti ti-user-plus"></i>
                Créer un compte
            </button>
        </div>

        <!-- Panneau Connexion -->
        <div id="panel-login" class="panel active">

            <?php if($success_msg): ?>
            <div class="success-box">
                <i class="ti ti-circle-check"></i>
                <?= $success_msg ?>
            </div>
            <?php endif; ?>

            <?php if($login_error): ?>
            <div class="error-box">
                <i class="ti ti-alert-circle"></i>
                <?= $login_error ?>
            </div>
            <?php endif; ?>

            <form action="login.php" method="POST">

                <div class="field">
                    <label>Nom d'utilisateur</label>
                    <div class="input-wrap">
                        <i class="ti ti-user"></i>
                        <input type="text" name="username" placeholder="Entrez votre identifiant" required>
                    </div>
                </div>

                <div class="field">
                    <label>Mot de passe</label>
                    <div class="input-wrap">
                        <i class="ti ti-lock"></i>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="forgot">
                    <a href="#">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="ti ti-login"></i>
                    Se connecter
                </button>

            </form>

            <div class="divider"><span>ou continuer avec</span></div>

            <a href="google-auth.php" class="btn-google">
                <svg width="18" height="18" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                Continuer avec Google
            </a>

        </div>

        <!-- Panneau Créer un compte -->
        <div id="panel-register" class="panel">

            <?php if($register_error): ?>
            <div class="error-box">
                <i class="ti ti-alert-circle"></i>
                <?= $register_error ?>
            </div>
            <?php endif; ?>

            <form action="register.php" method="POST">

                <div class="field">
                    <label>Nom complet</label>
                    <div class="input-wrap">
                        <i class="ti ti-user"></i>
                        <input type="text" name="fullname" placeholder="Prénom et nom" required>
                    </div>
                </div>

                <div class="field">
                    <label>Email universitaire</label>
                    <div class="input-wrap">
                        <i class="ti ti-mail"></i>
                        <input type="email" name="email" placeholder="prenom.nom@uasz.edu.sn" required>
                    </div>
                </div>

                <div class="field">
                    <label>Mot de passe</label>
                    <div class="input-wrap">
                        <i class="ti ti-lock"></i>
                        <input type="password" name="password" placeholder="Min. 8 caractères" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="ti ti-user-plus"></i>
                    Créer mon compte
                </button>

            </form>

            <div class="divider"><span>ou continuer avec</span></div>

            <a href="google-auth.php" class="btn-google">
                <svg width="18" height="18" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                S'inscrire avec Google
            </a>

            <p class="terms">
                En créant un compte, vous acceptez nos
                <a href="#">Conditions d'utilisation</a>.
            </p>

        </div>

    </div>

    <div class="footer">© 2026 Campus Care — Université Assane Seck de Ziguinchor</div>

</div>

<script>
// Ouvrir automatiquement le bon panneau selon l'URL
document.addEventListener('DOMContentLoaded', function() {
    <?php if($register_error): ?>
    showPanel('register');
    <?php elseif($success_msg): ?>
    showPanel('login');
    <?php endif; ?>
});

function showPanel(panel) {
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel-' + panel).classList.add('active');

    document.querySelector('.btn-login').classList.toggle('active', panel === 'login');
    document.querySelector('.btn-register').classList.toggle('active', panel === 'register');
}
</script>

</body>
</html>