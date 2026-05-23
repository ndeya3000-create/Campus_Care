<?php
require_once '../includes/db.php';
$id=(int)($_GET['id']??0);
if(!$id){header('Location: index.php');exit;}
$st=$pdo->prepare('SELECT * FROM t_patient WHERE id=?');$st->execute([$id]);$pat=$st->fetch();
if(!$pat){header('Location: index.php');exit;}
$page_title='Dossier — '.$pat['prenom'].' '.$pat['nom'];
require_once '../includes/header.php';

$rdvs=$pdo->prepare('SELECT r.*,CONCAT(m.prenom," ",m.nom) AS medecin,m.specialite FROM t_rendezvous r JOIN t_medecin m ON r.id_medecin=m.id WHERE r.id_patient=? ORDER BY r.date_heure DESC');
$rdvs->execute([$id]);$hrdv=$rdvs->fetchAll();

$cons=$pdo->prepare('SELECT c.*,r.date_heure,r.motif,CONCAT(m.prenom," ",m.nom) AS medecin,m.specialite FROM t_consultation c JOIN t_rendezvous r ON c.id_rdv=r.id JOIN t_medecin m ON r.id_medecin=m.id WHERE r.id_patient=? ORDER BY c.date_consultation DESC');
$cons->execute([$id]);$hcons=$cons->fetchAll();

$presc=$pdo->prepare('SELECT p.*,c.diagnostic,CONCAT(m.prenom," ",m.nom) AS medecin FROM t_prescription p JOIN t_consultation c ON p.id_consultation=c.id JOIN t_rendezvous r ON c.id_rdv=r.id JOIN t_medecin m ON r.id_medecin=m.id WHERE r.id_patient=? ORDER BY p.date_prescription DESC');
$presc->execute([$id]);$hpresc=$presc->fetchAll();

$init=strtoupper(substr($pat['prenom'],0,1).substr($pat['nom'],0,1));
$age=$pat['date_naissance']?(int)date_diff(date_create($pat['date_naissance']),date_create('today'))->y:null;
$bs_rdv=['planifie'=>['badge-blue','Planifié'],'confirme'=>['badge-green','Confirmé'],'annule'=>['badge-red','Annulé'],'termine'=>['badge-gray','Terminé']];
$bs_presc=['en_attente'=>['badge-orange','En attente'],'delivre'=>['badge-green','Délivré'],'partiel'=>['badge-blue','Partiel']];
?>
<div class="page-header">
  <div><h1>Dossier médical</h1><div class="breadcrumb"><a href="index.php">Patients</a> › Dossier</div></div>
  <div style="display:flex;gap:10px">
    <a href="pdf_dossier.php?id=<?=$id?>" target="_blank" class="btn btn-success">🖨️ Dossier PDF</a>
    <a href="modifier.php?id=<?=$id?>" class="btn btn-warning">Modifier</a>
    <a href="index.php" class="btn btn-outline">← Retour</a>
  </div>
</div>

<!-- Bannière identité -->
<div class="dossier-banner">
  <div class="dossier-av"><?=$init?></div>
  <div class="dossier-info">
    <h2><?=htmlspecialchars($pat['prenom'].' '.strtoupper($pat['nom']))?></h2>
    <p><?=htmlspecialchars($pat['matricule'])?> · <?=$pat['sexe']==='F'?'Féminin':'Masculin'?><?=$age?" · $age ans":''?><?=$pat['groupe_sanguin']?" · Groupe ".$pat['groupe_sanguin']:''?></p>
  </div>
  <div class="dossier-meta" style="margin-left:auto;text-align:right;font-size:12px;opacity:.85">
    <div><?=htmlspecialchars($pat['telephone']??'—')?></div>
    <div><?=htmlspecialchars($pat['email']??'—')?></div>
    <div style="margin-top:6px;background:rgba(255,255,255,0.18);padding:3px 12px;border-radius:20px;display:inline-block;font-weight:600">
      <?=$pat['type_patient']==='etudiant'?'Étudiant(e)':'Personnel'?>
    </div>
  </div>
</div>

<!-- Stats -->
<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);max-width:500px;margin-bottom:24px">
  <div class="stat-card"><div class="stat-icon blue"><svg width="20" height="20" fill="none" stroke="#1a56db" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div><div><div class="stat-label">Rendez-vous</div><div class="stat-value"><?=count($hrdv)?></div></div></div>
  <div class="stat-card"><div class="stat-icon green"><svg width="20" height="20" fill="none" stroke="#0e9f6e" stroke-width="2" viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div><div><div class="stat-label">Consultations</div><div class="stat-value"><?=count($hcons)?></div></div></div>
  <div class="stat-card"><div class="stat-icon orange"><svg width="20" height="20" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div><div><div class="stat-label">Prescriptions</div><div class="stat-value"><?=count($hpresc)?></div></div></div>
</div>

<div class="two-col" style="gap:20px;margin-bottom:20px">
  <!-- Timeline consultations -->
  <div class="card">
    <div class="card-head"><span class="card-title">Historique des consultations</span></div>
    <div class="card-body">
      <?php if(empty($hcons)):?>
        <div class="empty-state"><p>Aucune consultation enregistrée.</p></div>
      <?php else:?>
        <div class="timeline">
          <?php foreach($hcons as $c):?>
          <div class="tl-item">
            <div class="tl-dot"></div>
            <div class="tl-box">
              <div class="tl-date"><?=date('d/m/Y',strtotime($c['date_consultation']))?> · Dr <?=htmlspecialchars($c['medecin'])?></div>
              <div class="tl-title"><?=htmlspecialchars($c['diagnostic']??'Diagnostic non renseigné')?></div>
              <?php if($c['observations']):?><div class="tl-body"><?=nl2br(htmlspecialchars($c['observations']))?></div><?php endif;?>
              <div style="margin-top:8px;display:flex;gap:6px;flex-wrap:wrap">
                <?php if($c['tension']):?><span class="badge badge-blue">TA: <?=htmlspecialchars($c['tension'])?></span><?php endif;?>
                <?php if($c['temperature']):?><span class="badge badge-orange">T°: <?=$c['temperature']?>°C</span><?php endif;?>
              </div>
            </div>
          </div>
          <?php endforeach;?>
        </div>
      <?php endif;?>
    </div>
  </div>

  <!-- Tableau RDV -->
  <div class="card">
    <div class="card-head"><span class="card-title">Rendez-vous</span>
      <a href="/campuscare/rendezvous/ajouter.php?patient=<?=$id?>" class="btn btn-sm btn-primary">+ RDV</a>
    </div>
    <div style="padding:0">
      <?php if(empty($hrdv)):?>
        <div class="empty-state"><p>Aucun rendez-vous.</p></div>
      <?php else:?>
        <table class="data-table">
          <thead><tr><th>Date</th><th>Médecin</th><th>Statut</th></tr></thead>
          <tbody>
          <?php foreach($hrdv as $r):[$bc,$bl]=$bs_rdv[$r['statut']]??['badge-gray',$r['statut']];?>
            <tr>
              <td><strong><?=date('d/m/Y',strtotime($r['date_heure']))?></strong><div style="font-size:11px;color:var(--gris-5)"><?=date('H:i',strtotime($r['date_heure']))?></div></td>
              <td><div style="font-size:12px">Dr <?=htmlspecialchars($r['medecin'])?></div><div style="font-size:11px;color:var(--gris-5)"><?=htmlspecialchars($r['specialite'])?></div></td>
              <td><span class="badge <?=$bc?>"><?=$bl?></span></td>
            </tr>
          <?php endforeach;?>
          </tbody>
        </table>
      <?php endif;?>
    </div>
  </div>
</div>

<!-- Prescriptions -->
<?php if(!empty($hpresc)):?>
<div class="card">
  <div class="card-head"><span class="card-title">Prescriptions</span></div>
  <div style="padding:0">
    <table class="data-table">
      <thead><tr><th>Date</th><th>Médecin</th><th>Diagnostic associé</th><th>État</th></tr></thead>
      <tbody>
      <?php foreach($hpresc as $pr):[$bc,$bl]=$bs_presc[$pr['statut']]??['badge-gray',$pr['statut']];?>
        <tr>
          <td><?=date('d/m/Y',strtotime($pr['date_prescription']))?></td>
          <td>Dr <?=htmlspecialchars($pr['medecin'])?></td>
          <td><?=htmlspecialchars($pr['diagnostic']??'—')?></td>
          <td><span class="badge <?=$bc?>"><?=$bl?></span></td>
        </tr>
      <?php endforeach;?>
      </tbody>
    </table>
  </div>
</div>
<?php endif;?>

<?php require_once '../includes/footer.php';?>
