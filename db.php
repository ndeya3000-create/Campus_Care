<?php
session_start();

// ── Messages ──────────────────────────────────────────────────
$success_msg = '';
$error_msg   = '';

if (isset($_GET['success'])) {
    $success_msg = "Patient enregistré avec succès ! (ID : " . intval($_GET['id']) . ")";
}
if (isset($_GET['error'])) {
    $error_msg = htmlspecialchars(urldecode($_GET['error']));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Patient — Campus Care</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            padding: 30px 16px;
        }

        .page-wrap {
            max-width: 680px;
            margin: 0 auto;
        }

        /* ── En-tête ── */
        .page-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
        }

        .page-header .icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #1d4ed8, #0e7490);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            flex-shrink: 0;
        }

        .page-header h1 {
            font-size: 22px;
            font-weight: 700;
            color: #0f2756;
        }

        .page-header p {
            font-size: 13px;
            color: #64748b;
            margin-top: 2px;
        }

        /* ── Carte formulaire ── */
        .card {
            background: white;
            border-radius: 18px;
            padding: 32px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }

        /* ── Section titre ── */
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #1d4ed8;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin: 24px 0 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }

        .section-title:first-of-type { margin-top: 0; }

        /* ── Grille ── */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .full { grid-column: 1 / -1; }

        /* ── Champ ── */
        .field { display: flex; flex-direction: column; gap: 6px; }

        .field label {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            letter-spacing: 0.03em;
        }

        .field label span { color: #ef4444; margin-left: 2px; }

        .input-wrap { position: relative; display: flex; align-items: center; }

        .input-wrap i {
            position: absolute;
            left: 12px;
            font-size: 17px;
            color: #94a3b8;
            pointer-events: none;
        }

        .input-wrap input,
        .input-wrap select,
        .input-wrap textarea {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            font-size: 14px;
            color: #1e293b;
            background: #f8fafc;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .input-wrap textarea {
            resize: vertical;
            min-height: 80px;
            padding-top: 12px;
        }

        .input-wrap input:focus,
        .input-wrap select:focus,
        .input-wrap textarea:focus {
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
        }

        .input-wrap input::placeholder,
        .input-wrap textarea::placeholder { color: #b0bec5; }

        /* ── Messages ── */
        .success-box, .error-box {
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .success-box {
            background: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
        }

        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        /* ── Boutons ── */
        .btn-row {
            display: flex;
            gap: 12px;
            margin-top: 28px;
        }

        .btn-submit {
            flex: 1;
            padding: 13px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #1d4ed8, #0e7490);
            color: white;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: opacity 0.2s, transform 0.15s;
        }

        .btn-submit:hover  { opacity: 0.91; transform: translateY(-1px); }
        .btn-submit:active { transform: scale(0.98); }

        .btn-reset {
            padding: 13px 20px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            color: #64748b;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .btn-reset:hover { border-color: #94a3b8; }

        @media (max-width: 520px) {
            .grid-2 { grid-template-columns: 1fr; }
            .full    { grid-column: 1; }
        }
    </style>
</head>
<body>

<div class="page-wrap">

    <!-- En-tête -->
    <div class="page-header">
        <div class="icon"><i class="ti ti-user-plus"></i></div>
        <div>
            <h1>Nouveau Patient</h1>
            <p>Enregistrer un étudiant ou un membre du personnel</p>
        </div>
    </div>

    <div class="card">

        <!-- Messages -->
        <?php if ($success_msg): ?>
        <div class="success-box">
            <i class="ti ti-circle-check"></i>
            <?= $success_msg ?>
        </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
        <div class="error-box">
            <i class="ti ti-alert-circle"></i>
            <?= $error_msg ?>
        </div>
        <?php endif; ?>

        <!-- Formulaire -->
        <form action="patient_save.php" method="POST">

            <!-- Informations principales -->
            <p class="section-title">Informations principales</p>
            <div class="grid-2">

                <div class="field">
                    <label>Matricule <span>*</span></label>
                    <div class="input-wrap">
                        <i class="ti ti-id-badge"></i>
                        <input type="text" name="matricule" placeholder="Ex: ETU2026001" required>
                    </div>
                </div>

                <div class="field">
                    <label>Type de patient <span>*</span></label>
                    <div class="input-wrap">
                        <i class="ti ti-users"></i>
                        <select name="type_patient">
                            <option value="etudiant">Étudiant</option>
                            <option value="personnel">Personnel</option>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label>Nom <span>*</span></label>
                    <div class="input-wrap">
                        <i class="ti ti-user"></i>
                        <input type="text" name="nom" placeholder="Nom de famille" required>
                    </div>
                </div>

                <div class="field">
                    <label>Prénom <span>*</span></label>
                    <div class="input-wrap">
                        <i class="ti ti-user"></i>
                        <input type="text" name="prenom" placeholder="Prénom" required>
                    </div>
                </div>

                <div class="field">
                    <label>Date de naissance</label>
                    <div class="input-wrap">
                        <i class="ti ti-calendar"></i>
                        <input type="date" name="date_naissance">
                    </div>
                </div>

                <div class="field">
                    <label>Sexe</label>
                    <div class="input-wrap">
                        <i class="ti ti-gender-bigender"></i>
                        <select name="sexe">
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </div>
                </div>

            </div>

            <!-- Coordonnées -->
            <p class="section-title">Coordonnées</p>
            <div class="grid-2">

                <div class="field">
                    <label>Téléphone</label>
                    <div class="input-wrap">
                        <i class="ti ti-phone"></i>
                        <input type="tel" name="telephone" placeholder="Ex: 77 123 45 67">
                    </div>
                </div>

                <div class="field">
                    <label>Email</label>
                    <div class="input-wrap">
                        <i class="ti ti-mail"></i>
                        <input type="email" name="email" placeholder="exemple@uasz.edu.sn">
                    </div>
                </div>

                <div class="field full">
                    <label>Adresse</label>
                    <div class="input-wrap">
                        <i class="ti ti-map-pin"></i>
                        <textarea name="adresse" placeholder="Adresse complète"></textarea>
                    </div>
                </div>

            </div>

            <!-- Informations médicales -->
            <p class="section-title">Informations médicales</p>
            <div class="grid-2">

                <div class="field">
                    <label>Groupe sanguin</label>
                    <div class="input-wrap">
                        <i class="ti ti-droplet"></i>
                        <select name="groupe_sanguin">
                            <option value="">-- Sélectionner --</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                </div>

            </div>

            <!-- Boutons -->
            <div class="btn-row">
                <button type="reset" class="btn-reset">
                    <i class="ti ti-refresh"></i> Réinitialiser
                </button>
                <button type="submit" class="btn-submit">
                    <i class="ti ti-device-floppy"></i>
                    Enregistrer le patient
                </button>
            </div>

        </form>

    </div>
</div>

</body>
</html>