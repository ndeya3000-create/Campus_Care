<?php
require_once '../includes/db.php';
$page_title = 'Patients';
require_once '../includes/header.php';

$q    = trim($_GET['q']??'');
$type = $_GET['type']??'';
$pg   = max(1,(int)($_GET['page']??1));
$pp   = 8; $off = ($pg-1)*$pp;

$w=[]; $par=[];
if($q!==''){$w[]='(nom LIKE ? OR prenom LIKE ? OR matricule LIKE ? OR telephone LIKE ?)';$s="%$q%";array_push($par,$s,$s,$s,$s);}
if($type!==''){$w[]='type_patient=?';$par[]=$type;}
$wh=$w?'WHERE '.implode(' AND ',$w):'';

$total=(int)$pdo->prepare("SELECT COUNT(*) FROM t_patient $wh")->execute($par)&&($st=$pdo->prepare("SELECT COUNT(*) FROM t_patient $wh"))&&$st->execute($par)?$st->fetchColumn():0;
$st=$pdo->prepare("SELECT COUNT(*) FROM t_patient $wh");$st->execute($par);$total=(int)$st->fetchColumn();
$np=max(1,ceil($total/$pp));

$st=$pdo->prepare("SELECT *,(SELECT COUNT(*) FROM t_rendezvous r WHERE r.id_patient=t_patient.id) AS nb_rdv FROM t_patient $wh ORDER BY nom,prenom LIMIT $pp OFFSET $off");
$st->execute($par);$patients=$st->fetchAll();

$msg=$_GET['msg']??'';
$bs=['etudiant'=>['badge-blue','Étudiant(e)'],'personnel'=>['badge-orange','Personnel']];
?>
<?php if($msg==='ajoute'):?><div class="alert alert-success">✓ Patient ajouté avec succès.</div><?php endif;?>
<?php if($msg==='modifie'):?><div class="alert alert-success">✓ Patient modifié.</div><?php endif;?>
<?php if($msg==='supprime'):?><div class="alert alert-success">✓ Patient supprimé.</div><?php endif;?>

<div class="page-header">
  <div><h1>Patients</h1><div class="breadcrumb">Accueil › Patients</div></div>
  <a href="ajouter.php" class="btn btn-success">+ Nouveau patient</a>
</div>

<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);max-width:480px;margin-bottom:20px">
<?php
$tots=$pdo->query("SELECT COUNT(*) FROM t_patient")->fetchColumn();
$ets=$pdo->query("SELECT COUNT(*) FROM t_patient WHERE type_patient='etudiant'")->fetchColumn();
$pes=$pdo->query("SELECT COUNT(*) FROM t_patient WHERE type_patient='personnel'")->fetchColumn();
?>
  <div class="stat-card"><div class="stat-icon blue"><svg width="20" height="20" fill="none" stroke="#1a56db" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><div><div class="stat-label">Total</div><div class="stat-value"><?=$tots?></div></div></div>
  <div class="stat-card"><div class="stat-icon green"><svg width="20" height="20" fill="none" stroke="#0e9f6e" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/></svg></div><div><div class="stat-label">Étudiants</div><div class="stat-value"><?=$ets?></div></div></div>
  <div class="stat-card"><div class="stat-icon orange"><svg width="20" height="20" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/></svg></div><div><div class="stat-label">Personnel</div><div class="stat-value"><?=$pes?></div></div></div>
</div>

<div class="table-wrapper">
  <div class="table-toolbar">
    <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
      <div class="search-wrap">
        <svg class="search-ico" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="q" value="<?=htmlspecialchars($q)?>" placeholder="Nom, matricule, téléphone...">
      </div>
      <select name="type" style="padding:8px 12px;border:1px solid var(--gris-3);border-radius:var(--radius);font-size:13px">
        <option value="">Tous types</option>
        <option value="etudiant" <?=$type==='etudiant'?'selected':''?>>Étudiant(e)</option>
        <option value="personnel" <?=$type==='personnel'?'selected':''?>>Personnel</option>
      </select>
      <button type="submit" class="btn btn-outline">Filtrer</button>
      <?php if($q||$type):?><a href="index.php" class="btn btn-outline">✕ Reset</a><?php endif;?>
    </form>
    <span style="font-size:12px;color:var(--gris-5)"><?=$total?> résultat<?=$total>1?'s':''?></span>
  </div>
  <table class="data-table">
    <thead><tr><th>#</th><th>Patient</th><th>Matricule</th><th>Type</th><th>Téléphone</th><th>Groupe sg.</th><th>RDV</th><th style="text-align:center">Actions</th></tr></thead>
    <tbody>
    <?php if(empty($patients)):?>
      <tr><td colspan="8"><div class="empty-state"><p>Aucun patient trouvé.</p></div></td></tr>
    <?php else: foreach($patients as $i=>$p):
      $init=strtoupper(substr($p['prenom'],0,1).substr($p['nom'],0,1));
      $avs=['av-blue','av-green','av-orange']; $av=$avs[$p['id']%3];
      [$bc,$bl]=$bs[$p['type_patient']]??['badge-gray',$p['type_patient']];
    ?>
    <tr>
      <td style="color:var(--gris-4);font-size:12px"><?=$off+$i+1?></td>
      <td><div class="flex-center gap-8">
        <div class="avatar <?=$av?>"><?=$init?></div>
        <div><div style="font-weight:600"><?=htmlspecialchars($p['prenom'].' '.$p['nom'])?></div>
        <div style="font-size:11px;color:var(--gris-5)"><?=htmlspecialchars($p['email']??'')?></div></div>
      </div></td>
      <td><span class="tag"><?=htmlspecialchars($p['matricule'])?></span></td>
      <td><span class="badge <?=$bc?>"><?=$bl?></span></td>
      <td><?=htmlspecialchars($p['telephone']??'—')?></td>
      <td><?=$p['groupe_sanguin']?'<span class="badge badge-red">'.htmlspecialchars($p['groupe_sanguin']).'</span>':'<span style="color:var(--gris-4)">—</span>'?></td>
      <td><span class="badge <?=(int)$p['nb_rdv']>0?'badge-green':'badge-gray'?>"><?=$p['nb_rdv']?> RDV</span></td>
      <td><div class="actions" style="justify-content:center">
        <a href="dossier.php?id=<?=$p['id']?>" class="btn btn-sm btn-outline" title="Dossier">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </a>
        <a href="modifier.php?id=<?=$p['id']?>" class="btn btn-sm btn-warning" title="Modifier">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </a>
        <button class="btn btn-sm btn-danger" title="Supprimer"
          onclick="confirmerSuppression('supprimer.php?id=<?=$p['id']?>','<?=htmlspecialchars(addslashes($p['prenom'].' '.$p['nom']))?>')">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
        </button>
      </div></td>
    </tr>
    <?php endforeach; endif;?>
    </tbody>
  </table>
  <?php if($np>1):?>
  <div class="pagination">
    <span style="font-size:12px;color:var(--gris-5)"><?=$total?> patient<?=$total>1?'s':''?></span>
    <div class="pagination-links">
      <?php if($pg>1):?><a href="?q=<?=urlencode($q)?>&type=<?=$type?>&page=<?=$pg-1?>">‹</a><?php endif;?>
      <?php for($x=1;$x<=$np;$x++):?>
        <?php if($x===$pg):?><span class="pg-active"><?=$x?></span>
        <?php else:?><a href="?q=<?=urlencode($q)?>&type=<?=$type?>&page=<?=$x?>"><?=$x?></a><?php endif;?>
      <?php endfor;?>
      <?php if($pg<$np):?><a href="?q=<?=urlencode($q)?>&type=<?=$type?>&page=<?=$pg+1?>">›</a><?php endif;?>
    </div>
  </div>
  <?php endif;?>
</div>
<?php require_once '../includes/footer.php';?>
