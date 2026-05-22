<?php
// Vérification que l'admin est connecté (à activer quand la page login sera prête)
// session_start();
// if (!isset($_SESSION['admin'])) header('Location: login.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Centre de Santé Universitaire</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <style>
        :root {
            --bg:        #0d0f1a;
            --surface:   #151828;
            --card:      #1c2035;
            --border:    #2a2f4a;
            --cyan:      #00e5ff;
            --purple:    #a855f7;
            --green:     #22d3a5;
            --orange:    #ff6b35;
            --pink:      #f72585;
            --yellow:    #ffd60a;
            --text:      #e8eaf6;
            --muted:     #7c83a8;
            --font-head: 'Syne', sans-serif;
            --font-body: 'DM Sans', sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-body);
            min-height: 100vh;
            display: flex;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 28px 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
        }

        .logo {
            padding: 0 24px 32px;
            border-bottom: 1px solid var(--border);
        }

        .logo-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--cyan), var(--purple));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            margin-bottom: 12px;
        }

        .logo h1 {
            font-family: var(--font-head);
            font-size: 15px;
            font-weight: 800;
            color: var(--text);
            line-height: 1.3;
        }

        .logo span {
            font-size: 11px;
            color: var(--cyan);
            font-weight: 500;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .nav { padding: 24px 16px; flex: 1; }

        .nav-section {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--muted);
            padding: 0 8px;
            margin: 20px 0 8px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            border-radius: 10px;
            color: var(--muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            cursor: pointer;
            margin-bottom: 2px;
        }

        .nav-item:hover { background: var(--card); color: var(--text); }

        .nav-item.active {
            background: linear-gradient(135deg, rgba(0,229,255,0.15), rgba(168,85,247,0.15));
            color: var(--cyan);
            border: 1px solid rgba(0,229,255,0.2);
        }

        .nav-item .icon { font-size: 18px; width: 22px; text-align: center; }

        .nav-badge {
            margin-left: auto;
            background: var(--pink);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 20px;
        }

        .sidebar-footer {
            padding: 20px 16px;
            border-top: 1px solid var(--border);
        }

        .admin-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background: var(--card);
            border-radius: 10px;
        }

        .avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--cyan), var(--purple));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800;
            font-size: 14px;
            color: var(--bg);
        }

        .admin-info p  { font-size: 13px; font-weight: 600; }
        .admin-info span { font-size: 11px; color: var(--cyan); }

        /* ── MAIN ── */
        .main {
            margin-left: 260px;
            flex: 1;
            padding: 32px;
            max-width: calc(100vw - 260px);
        }

        /* ── HEADER ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 36px;
        }

        .header-left h2 {
            font-family: var(--font-head);
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(90deg, var(--cyan), var(--purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-left p {
            color: var(--muted);
            font-size: 14px;
            margin-top: 4px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-family: var(--font-body);
            transition: all 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--cyan), var(--purple));
            color: var(--bg);
        }

        .btn-primary:hover { opacity: 0.85; transform: translateY(-1px); }

        .btn-ghost {
            background: var(--card);
            color: var(--text);
            border: 1px solid var(--border);
        }

        .btn-ghost:hover { background: var(--border); }

        .notif-btn {
            position: relative;
            width: 40px; height: 40px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            font-size: 18px;
        }

        .notif-dot {
            position: absolute;
            top: 8px; right: 8px;
            width: 8px; height: 8px;
            background: var(--pink);
            border-radius: 50%;
            border: 2px solid var(--surface);
        }

        /* ── STATS CARDS ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 22px;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.4);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
        }

        .stat-card.cyan::before   { background: linear-gradient(90deg, var(--cyan), transparent); }
        .stat-card.purple::before { background: linear-gradient(90deg, var(--purple), transparent); }
        .stat-card.green::before  { background: linear-gradient(90deg, var(--green), transparent); }
        .stat-card.pink::before   { background: linear-gradient(90deg, var(--pink), transparent); }

        .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .stat-label {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--muted);
        }

        .stat-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
        }

        .stat-card.cyan   .stat-icon { background: rgba(0,229,255,0.12); }
        .stat-card.purple .stat-icon { background: rgba(168,85,247,0.12); }
        .stat-card.green  .stat-icon { background: rgba(34,211,165,0.12); }
        .stat-card.pink   .stat-icon { background: rgba(247,37,133,0.12); }

        .stat-value {
            font-family: var(--font-head);
            font-size: 36px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 6px;
        }

        .stat-card.cyan   .stat-value { color: var(--cyan); }
        .stat-card.purple .stat-value { color: var(--purple); }
        .stat-card.green  .stat-value { color: var(--green); }
        .stat-card.pink   .stat-value { color: var(--pink); }

        .stat-sub { font-size: 12px; color: var(--muted); }

        /* ── GRAPHIQUES ── */
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .panel {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .panel-title {
            font-family: var(--font-head);
            font-size: 16px;
            font-weight: 700;
        }

        .panel-badge {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-cyan { background: rgba(0,229,255,0.12);  color: var(--cyan); }
        .badge-pink { background: rgba(247,37,133,0.12); color: var(--pink); }

        .chart-wrap {
            position: relative;
            height: 250px;
        }

        /* ── ALERTES STOCK ── */
        .alerte-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(42,47,74,0.5);
        }

        .alerte-item:last-child { border-bottom: none; }

        .alerte-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
            background: var(--pink);
            box-shadow: 0 0 8px var(--pink);
        }

        .alerte-nom  { font-size: 13px; font-weight: 500; flex: 1; }

        .alerte-qty {
            font-family: var(--font-head);
            font-size: 14px;
            font-weight: 700;
            color: var(--pink);
        }

        /* ── EMPTY STATE ── */
        .empty {
            text-align: center;
            padding: 32px;
            color: var(--muted);
            font-size: 13px;
        }

        .empty span { font-size: 32px; display: block; margin-bottom: 8px; }

        /* ── ANIMATIONS ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .stat-card { animation: fadeUp 0.4s ease both; }
        .stat-card:nth-child(1) { animation-delay: 0.05s; }
        .stat-card:nth-child(2) { animation-delay: 0.10s; }
        .stat-card:nth-child(3) { animation-delay: 0.15s; }
        .stat-card:nth-child(4) { animation-delay: 0.20s; }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

        /* ── RESPONSIVE ── */
        @media (max-width: 1200px) {
            .stats-grid  { grid-template-columns: repeat(2, 1fr); }
            .charts-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar">
    <div class="logo">
        <div class="logo-icon">🏥</div>
        <h1>Centre de Santé<br>Universitaire</h1>
        <span>Admin Panel</span>
    </div>

    <nav class="nav">
        <div class="nav-section">Principal</div>
        <a class="nav-item active">
            <span class="icon">📊</span> Dashboard
        </a>
        <a class="nav-item" href="patients.php">
            <span class="icon">👥</span> Patients
        </a>
        <a class="nav-item" href="medecins.php">
            <span class="icon">👨‍⚕️</span> Médecins
        </a>

        <div class="nav-section">Soins</div>
        <a class="nav-item" href="rendez_vous.php">
            <span class="icon">📅</span> Rendez-vous
            <span class="nav-badge" id="rdv-badge">—</span>
        </a>
        <a class="nav-item" href="consultations.php">
            <span class="icon">🩺</span> Consultations
        </a>
        <a class="nav-item" href="prescriptions.php">
            <span class="icon">📋</span> Prescriptions
        </a>

        <div class="nav-section">Pharmacie</div>
        <a class="nav-item" href="pharmacie.php">
            <span class="icon">💊</span> Médicaments
            <span class="nav-badge" id="alerte-badge">—</span>
        </a>

        <div class="nav-section">Rapports</div>
        <a class="nav-item" href="statistiques.php">
            <span class="icon">📈</span> Statistiques
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="admin-card">
            <div class="avatar">AD</div>
            <div class="admin-info">
                <p>Administrateur</p>
                <span>En ligne</span>
            </div>
        </div>
    </div>
</aside>

<!-- ══ MAIN ══ -->
<main class="main">

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <h2>Tableau de Bord</h2>
            <p id="date-actuelle">Chargement...</p>
        </div>
        <div class="header-right">
            <div class="notif-btn" title="Alertes">
                🔔
                <div class="notif-dot" id="notif-dot" style="display:none"></div>
            </div>
            <button class="btn btn-ghost" onclick="chargerDonnees()">🔄 Actualiser</button>
        </div>
    </div>

    <!-- 4 Cartes de statistiques -->
    <div class="stats-grid">

        <div class="stat-card cyan">
            <div class="stat-top">
                <span class="stat-label">Total Patients</span>
                <div class="stat-icon">👥</div>
            </div>
            <div class="stat-value" id="val-patients">—</div>
            <div class="stat-sub">Patients enregistrés</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-top">
                <span class="stat-label">Médecins</span>
                <div class="stat-icon">👨‍⚕️</div>
            </div>
            <div class="stat-value" id="val-medecins">—</div>
            <div class="stat-sub">Médecins actifs</div>
        </div>

        <div class="stat-card green">
            <div class="stat-top">
                <span class="stat-label">RDV Aujourd'hui</span>
                <div class="stat-icon">📅</div>
            </div>
            <div class="stat-value" id="val-rdv">—</div>
            <div class="stat-sub">Rendez-vous du jour</div>
        </div>

        <div class="stat-card pink">
            <div class="stat-top">
                <span class="stat-label">Alertes Stock</span>
                <div class="stat-icon">⚠️</div>
            </div>
            <div class="stat-value" id="val-alerte">—</div>
            <div class="stat-sub">Médicaments en rupture</div>
        </div>

    </div>

    <!-- Graphiques -->
    <div class="charts-grid">

        <!-- Graphique consultations par mois -->
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">📈 Consultations par mois</span>
                <span class="panel-badge badge-cyan">Cette année</span>
            </div>
            <div class="chart-wrap">
                <canvas id="chartConsultations"></canvas>
            </div>
        </div>

        <!-- Alertes médicaments en rupture -->
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">⚠️ Médicaments en rupture</span>
                <span class="panel-badge badge-pink">Alertes</span>
            </div>
            <div id="alerte-list">
                <div class="empty"><span>⏳</span>Chargement...</div>
            </div>
        </div>

    </div>

</main>

<script>
// ── Date actuelle ──
const jours = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
const mois  = ['Janvier','Février','Mars','Avril','Mai','Juin',
               'Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
const now   = new Date();
document.getElementById('date-actuelle').textContent =
    `${jours[now.getDay()]} ${now.getDate()} ${mois[now.getMonth()]} ${now.getFullYear()}`;

let chartInstance = null;

// ── Chargement des données depuis api_dashboard.php ──
async function chargerDonnees() {
    try {
        const res  = await fetch('api_dashboard.php');
        const data = await res.json();

        if (data.error) {
            console.error('Erreur API :', data.error);
            return;
        }

        // Mise à jour des 4 cartes
        document.getElementById('val-patients').textContent = data.total_patients;
        document.getElementById('val-medecins').textContent = data.total_medecins;
        document.getElementById('val-rdv').textContent      = data.rdv_aujourdhui;
        document.getElementById('val-alerte').textContent   = data.medicaments_alerte;

        // Badges sidebar
        document.getElementById('rdv-badge').textContent    = data.rdv_aujourdhui;
        document.getElementById('alerte-badge').textContent = data.medicaments_alerte;

        // Cloche de notification si alertes
        if (parseInt(data.medicaments_alerte) > 0) {
            document.getElementById('notif-dot').style.display = 'block';
        }

        // Graphique
        afficherGraphique(data.graphique_consultations);

        // Liste alertes stock
        afficherAlertes(data.alertes_stock);

    } catch (err) {
        console.error('Erreur réseau :', err);
    }
}

// ── Graphique Chart.js ──
function afficherGraphique(donnees) {
    const nomsMois = ['Jan','Fév','Mar','Avr','Mai','Juin',
                      'Juil','Août','Sep','Oct','Nov','Déc'];

    // 12 mois avec 0 par défaut
    const valeurs = Array(12).fill(0);
    if (donnees) {
        donnees.forEach(d => {
            valeurs[parseInt(d.mois) - 1] = parseInt(d.total);
        });
    }

    const ctx = document.getElementById('chartConsultations').getContext('2d');
    if (chartInstance) chartInstance.destroy();

    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: nomsMois,
            datasets: [{
                label: 'Consultations',
                data: valeurs,
                backgroundColor: (ctx) => {
                    const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 250);
                    g.addColorStop(0, 'rgba(0,229,255,0.8)');
                    g.addColorStop(1, 'rgba(168,85,247,0.2)');
                    return g;
                },
                borderColor: '#00e5ff',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1c2035',
                    borderColor: '#2a2f4a',
                    borderWidth: 1,
                    titleColor: '#00e5ff',
                    bodyColor: '#e8eaf6',
                    padding: 12,
                }
            },
            scales: {
                x: {
                    grid:  { color: 'rgba(42,47,74,0.5)' },
                    ticks: { color: '#7c83a8', font: { size: 12 } }
                },
                y: {
                    grid:  { color: 'rgba(42,47,74,0.5)' },
                    ticks: { color: '#7c83a8', font: { size: 12 }, stepSize: 1 },
                    beginAtZero: true
                }
            }
        }
    });
}

// ── Liste alertes médicaments ──
function afficherAlertes(items) {
    const el = document.getElementById('alerte-list');
    if (!items || items.length === 0) {
        el.innerHTML = '<div class="empty"><span>✅</span>Aucun stock en rupture</div>';
        return;
    }
    el.innerHTML = items.map(item => `
        <div class="alerte-item">
            <div class="alerte-dot"></div>
            <div class="alerte-nom">${item.nom_medicament}</div>
            <div class="alerte-qty">${item.quantite_stock} unités</div>
        </div>
    `).join('');
}

// ── Lancement au chargement de la page ──
chargerDonnees();

// ── Actualisation automatique toutes les 60 secondes ──
setInterval(chargerDonnees, 60000);
</script>

</body>
</html>