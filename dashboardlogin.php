<?php
session_start();

// ── Vérifier que l'utilisateur est connecté ───────────────────
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord — Campus Care</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 48px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
            max-width: 480px;
            width: 90%;
        }
        .icon { font-size: 56px; margin-bottom: 16px; }
        h1 { color: #0f2756; font-size: 24px; margin-bottom: 8px; }
        p  { color: #64748b; font-size: 15px; margin-bottom: 28px; }
        .badge {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 28px;
            text-transform: capitalize;
        }
        .btn-logout {
            display: inline-block;
            padding: 12px 28px;
            background: #ef4444;
            color: white;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: background 0.2s;
        }
        .btn-logout:hover { background: #dc2626; }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">🏥</div>
    <h1>Bienvenue, <?= htmlspecialchars($_SESSION['full_name']) ?> !</h1>
    <p>Vous êtes connecté à Campus Care.</p>
    <div class="badge"><?= htmlspecialchars($_SESSION['role']) ?></div>
    <br>
    <a href="../logout.php" class="btn-logout">Se déconnecter</a>
</div>
</body>
</html>