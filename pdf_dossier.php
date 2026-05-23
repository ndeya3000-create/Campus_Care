<?php
/**
 * patients/pdf_dossier.php
 * Dossier médical complet imprimable — Ctrl+P → Enregistrer en PDF
 */
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) { header('Location: /campuscare/login.php'); exit; }
require_once '../includes/db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$st = $pdo->prepare('SELECT * FROM t_patient WHERE id=?');
$st->execute([$id]); $pat = $st->fetch();
if (!$pat) { header('Location: index.php'); exit; }

// Consultations
$cons = $pdo->prepare(
    "SELECT c.*,CONCAT(m.prenom,' ',m.nom) AS medecin,m.specialite
     FROM t_consultation c
     JOIN t_rendezvous r ON c.id_rdv=r.id
     JOIN t_medecin m ON r.id_medecin=m.id
     WHERE r.id_patient=? ORDER BY c.date_consultation DESC"
);
$cons->execute([$id]); $consultations = $cons->fetchAll();

// RDV
$rdvs = $pdo->prepare(
    "SELECT r.*,CONCAT(m.prenom,' ',m.nom) AS medecin,m.specialite
     FROM t_rendezvous r JOIN t_medecin m ON r.id_medecin=m.id
     WHERE r.id_patient=? ORDER BY r.date_heure DESC"
);
$rdvs->execute([$id]); $rendezvous = $rdvs->fetchAll();

// Prescriptions
$pres = $pdo->prepare(
    "SELECT p.*,c.diagnostic,CONCAT(m.prenom,' ',m.nom) AS medecin,
            GROUP_CONCAT(CONCAT(med.nom,' ',med.dosage,' — ',lp.posologie) SEPARATOR '\n') AS details
     FROM t_prescription p
     JOIN t_consultation c ON p.id_consultation=c.id
     JOIN t_rendezvous r ON c.id_rdv=r.id
     JOIN t_medecin m ON r.id_medecin=m.id
     LEFT JOIN t_ligne_prescription lp ON lp.id_prescription=p.id
     LEFT JOIN t_medicament med ON lp.id_medicament=med.id
     WHERE r.id_patient=? GROUP BY p.id ORDER BY p.date_prescription DESC"
);
$pres->execute([$id]); $prescriptions = $pres->fetchAll();

$init = strtoupper(substr($pat['prenom'],0,1).substr($pat['nom'],0,1));
$age  = $pat['date_naissance']
    ? (int)date_diff(date_create($pat['date_naissance']), date_create('today'))->y
    : null;

$bs_rdv = ['planifie'=>'Planifié','confirme'=>'Confirmé','annule'=>'Annulé','termine'=>'Terminé'];
$bs_presc = ['en_attente'=>'En attente','delivre'=>'Délivré','partiel'=>'Partiel'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dossier médical — <?=htmlspecialchars($pat['prenom'].' '.$pat['nom'])?></title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, sans-serif; font-size: 13px; color: #1a1a2e; background: #fff; }
  .page { max-width: 760px; margin: 0 auto; padding: 40px; }

  /* Header */
  .doc-header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 18px; border-bottom: 3px solid #1e3a5f; margin-bottom: 24px; }
  .brand-name { font-size: 22px; font-weight: 800; color: #1e3a5f; }
  .brand-name span { color: #0e9f6e; }
  .brand-sub { font-size: 11px; color: #9ca3af; margin-top: 3px; }
  .doc-type { text-align: right; }
  .doc-type h1 { font-size: 17px; font-weight: 700; color: #1e3a5f; }
  .doc-type p  { font-size: 11px; color: #9ca3af; margin-top: 3px; }

  /* Identité patient */
  .patient-banner { background: linear-gradient(135deg, #1e3a5f, #1a56db, #0e9f6e); color: #fff; border-radius: 10px; padding: 20px 24px; display: flex; align-items: center; gap: 18px; margin-bottom: 24px; }
  .pat-avatar { width: 56px; height: 56px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; flex-shrink: 0; }
  .pat-name { font-size: 18px; font-weight: 700; }
  .pat-detail { font-size: 12px; opacity: .8; margin-top: 4px; }
  .pat-meta { margin-left: auto; text-align: right; font-size: 12px; opacity: .85; }

  /* Sections */
  .section { margin-bottom: 28px; }
  .section-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #6b7280; padding-bottom: 8px; border-bottom: 2px solid #e5e7eb; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
  .section-title .count { background: #1e3a5f; color: #fff; padding: 2px 8px; border-radius: 20px; font-size: 10px; }

  /* Tableau */
  table { width: 100%; border-collapse: collapse; font-size: 12px; }
  th { background: #1e3a5f; color: #fff; padding: 8px 12px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
  td { padding: 9px 12px; border-bottom: 1px solid #f3f4f6; }
  tr:nth-child(even) td { background: #fafafa; }

  /* Timeline */
  .tl { position: relative; padding-left: 22px; }
  .tl::before { content: ''; position: absolute; left: 6px; top: 0; bottom: 0; width: 2px; background: #e5e7eb; }
  .tl-item { position: relative; margin-bottom: 16px; }
  .tl-dot { position: absolute; left: -22px; top: 4px; width: 12px; height: 12px; border-radius: 50%; background: #0e9f6e; border: 2px solid #fff; box-shadow: 0 0 0 2px #0e9f6e; }
  .tl-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px 14px; }
  .tl-date { font-size: 11px; color: #9ca3af; margin-bottom: 3px; }
  .tl-diag { font-size: 13px; font-weight: 700; color: #1e3a5f; }
  .tl-obs  { font-size: 12px; color: #6b7280; margin-top: 5px; line-height: 1.5; }
  .badge   { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 600; }
  .b-blue  { background: #ebf5ff; color: #1a56db; }
  .b-green { background: #e8f5f0; color: #057a55; }
  .b-red   { background: #fdf2f2; color: #e02424; }
  .b-gray  { background: #f3f4f6; color: #6b7280; }
  .b-orange{ background: #fffbeb; color: #d97706; }

  /* Stats summary */
  .stats-row { display: flex; gap: 16px; margin-bottom: 24px; }
  .stat-box { flex: 1; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 16px; text-align: center; }
  .stat-box .n { font-size: 24px; font-weight: 800; color: #1e3a5f; }
  .stat-box .l { font-size: 11px; color: #9ca3af; margin-top: 2px; }

  /* Footer */
  .doc-footer { margin-top: 40px; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #f3f4f6; padding-top: 14px; }

  /* Boutons */
  .print-btn { position: fixed; top: 20px; right: 20px; background: #1a56db; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(26,86,219,0.3); z-index: 100; }
  .back-btn  { position: fixed; top: 20px; right: 170px; background: #f3f4f6; color: #374151; border: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; cursor: pointer; z-index: 100; }

  @media print {
    .print-btn, .back-btn { display: none !important; }
    .page { padding: 20px; }
  }
</style>
</head>
<body>

<button class="back-btn" onclick="window.history.back()">← Retour</button>
<button class="print-btn" onclick="window.print()">🖨️ Imprimer / PDF</button>

<div class="page">

  <!-- En-tête -->
  <div class="doc-header">
    <div>
      <div class="brand-name">Campus<span>Care</span></div>
      <div class="brand-sub">Centre de Santé Universitaire — Ziguinchor</div>
      <div class="brand-sub">Université Assane Seck de Ziguinchor</div>
    </div>
    <div class="doc-type">
      <h1>DOSSIER MÉDICAL</h1>
      <p>Généré le <?=date('d/m/Y à H:i')?></p>
      <p>Dossier confidentiel — usage médical uniquement</p>
    </div>
  </div>

  <!-- Identité patient -->
  <div class="patient-banner">
    <div class="pat-avatar"><?=$init?></div>
    <div>
      <div class="pat-name"><?=htmlspecialchars($pat['prenom'].' '.strtoupper($pat['nom']))?></div>
      <div class="pat-detail">
        <?=htmlspecialchars($pat['matricule'])?>
        &nbsp;·&nbsp; <?=$pat['sexe']==='F'?'Féminin':'Masculin'?>
        <?=$age?" &nbsp;·&nbsp; $age ans":''?>
        <?=$pat['groupe_sanguin']?" &nbsp;·&nbsp; Groupe ".$pat['groupe_sanguin']:''?>
      </div>
      <div class="pat-detail" style="margin-top:4px">
        <?=$pat['type_patient']==='etudiant'?'Étudiant(e)':'Personnel universitaire'?>
        <?=$pat['telephone']?" &nbsp;·&nbsp; ".$pat['telephone']:''?>
      </div>
    </div>
    <div class="pat-meta">
      <?php if($pat['email']):?><div><?=htmlspecialchars($pat['email'])?></div><?php endif;?>
      <?php if($pat['adresse']):?><div><?=htmlspecialchars($pat['adresse'])?></div><?php endif;?>
    </div>
  </div>

  <!-- Résumé stats -->
  <div class="stats-row">
    <div class="stat-box"><div class="n"><?=count($rendezvous)?></div><div class="l">Rendez-vous</div></div>
    <div class="stat-box"><div class="n"><?=count($consultations)?></div><div class="l">Consultations</div></div>
    <div class="stat-box"><div class="n"><?=count($prescriptions)?></div><div class="l">Prescriptions</div></div>
  </div>

  <!-- Historique consultations -->
  <div class="section">
    <div class="section-title">
      Historique des consultations
      <span class="count"><?=count($consultations)?></span>
    </div>
    <?php if(empty($consultations)):?>
      <p style="color:#9ca3af;font-style:italic">Aucune consultation enregistrée.</p>
    <?php else:?>
      <div class="tl">
      <?php foreach($consultations as $c):?>
        <div class="tl-item">
          <div class="tl-dot"></div>
          <div class="tl-box">
            <div class="tl-date"><?=date('d/m/Y',strtotime($c['date_consultation']))?> &nbsp;·&nbsp; Dr <?=htmlspecialchars($c['medecin'])?> (<?=htmlspecialchars($c['specialite'])?>)</div>
            <div class="tl-diag"><?=htmlspecialchars($c['diagnostic']??'Diagnostic non renseigné')?></div>
            <?php if($c['observations']):?>
              <div class="tl-obs"><?=nl2br(htmlspecialchars($c['observations']))?></div>
            <?php endif;?>
            <div style="margin-top:7px;display:flex;gap:6px;flex-wrap:wrap">
              <?php if($c['tension']):?><span class="badge b-blue">TA : <?=htmlspecialchars($c['tension'])?></span><?php endif;?>
              <?php if($c['temperature']):?><span class="badge b-orange">T° : <?=$c['temperature']?>°C</span><?php endif;?>
            </div>
          </div>
        </div>
      <?php endforeach;?>
      </div>
    <?php endif;?>
  </div>

  <!-- Prescriptions -->
  <div class="section">
    <div class="section-title">
      Prescriptions médicales
      <span class="count"><?=count($prescriptions)?></span>
    </div>
    <?php if(empty($prescriptions)):?>
      <p style="color:#9ca3af;font-style:italic">Aucune prescription.</p>
    <?php else:?>
      <table>
        <thead>
          <tr><th>Date</th><th>Médecin</th><th>Diagnostic</th><th>Médicaments</th><th>État</th></tr>
        </thead>
        <tbody>
        <?php foreach($prescriptions as $pr):
          $etats=['en_attente'=>['b-orange','En attente'],'delivre'=>['b-green','Délivré'],'partiel'=>['b-blue','Partiel']];
          [$bc,$bl]=$etats[$pr['statut']]??['b-gray',$pr['statut']];
        ?>
          <tr>
            <td><?=date('d/m/Y',strtotime($pr['date_prescription']))?></td>
            <td>Dr <?=htmlspecialchars($pr['medecin'])?></td>
            <td><?=htmlspecialchars($pr['diagnostic']??'—')?></td>
            <td style="font-size:11px;white-space:pre-line"><?=htmlspecialchars($pr['details']??'—')?></td>
            <td><span class="badge <?=$bc?>"><?=$bl?></span></td>
          </tr>
        <?php endforeach;?>
        </tbody>
      </table>
    <?php endif;?>
  </div>

  <!-- Rendez-vous -->
  <div class="section">
    <div class="section-title">
      Rendez-vous
      <span class="count"><?=count($rendezvous)?></span>
    </div>
    <?php if(empty($rendezvous)):?>
      <p style="color:#9ca3af;font-style:italic">Aucun rendez-vous.</p>
    <?php else:?>
      <table>
        <thead><tr><th>Date & Heure</th><th>Médecin</th><th>Spécialité</th><th>Motif</th><th>Statut</th></tr></thead>
        <tbody>
        <?php foreach($rendezvous as $r):
          $smap=['planifie'=>['b-blue','Planifié'],'confirme'=>['b-green','Confirmé'],'annule'=>['b-red','Annulé'],'termine'=>['b-gray','Terminé']];
          [$bc,$bl]=$smap[$r['statut']]??['b-gray',$r['statut']];
        ?>
          <tr>
            <td><?=date('d/m/Y',strtotime($r['date_heure']))?><br><small style="color:#9ca3af"><?=date('H:i',strtotime($r['date_heure']))?></small></td>
            <td>Dr <?=htmlspecialchars($r['medecin'])?></td>
            <td><?=htmlspecialchars($r['specialite']??'—')?></td>
            <td><?=htmlspecialchars(substr($r['motif']??'—',0,40))?></td>
            <td><span class="badge <?=$bc?>"><?=$bl?></span></td>
          </tr>
        <?php endforeach;?>
        </tbody>
      </table>
    <?php endif;?>
  </div>

  <!-- Footer -->
  <div class="doc-footer">
    CampusCare — Centre de Santé Universitaire · Université Assane Seck de Ziguinchor<br>
    Document confidentiel · Généré le <?=date('d/m/Y à H:i')?> · Usage médical uniquement
  </div>

</div>
</body>
</html>
