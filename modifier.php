<?php
require_once '../includes/db.php';
$id=(int)($_GET['id']??0);
if(!$id){header('Location: index.php');exit;}
$st=$pdo->prepare('SELECT * FROM t_patient WHERE id=?');$st->execute([$id]);$p=$st->fetch();
if(!$p){header('Location: index.php');exit;}
$page_title='Modifier — '.$p['prenom'].' '.$p['nom'];
require_once '../includes/header.php';
$err=[];$d=$p;
if($_SERVER['REQUEST_METHOD']==='POST'){
  foreach(['matricule','nom','prenom','date_naissance','sexe','type_patient','telephone','email','adresse','groupe_sanguin'] as $k){$d[$k]=trim($_POST[$k]??'');}
  if(!$d['matricule'])$err[]='Matricule obligatoire.';
  if(!$d['nom'])$err[]='Nom obligatoire.';
  if(!$d['prenom'])$err[]='Prénom obligatoire.';
  if(empty($err)){
    $ex=$pdo->prepare('SELECT id FROM t_patient WHERE matricule=? AND id!=?');$ex->execute([$d['matricule'],$id]);
    if($ex->fetch())$err[]='Ce matricule est déjà utilisé par un autre patient.';
  }
  if(empty($err)){
    $pdo->prepare('UPDATE t_patient SET matricule=?,nom=?,prenom=?,date_naissance=?,sexe=?,type_patient=?,telephone=?,email=?,adresse=?,groupe_sanguin=? WHERE id=?')
      ->execute([$d['matricule'],strtoupper($d['nom']),ucfirst(strtolower($d['prenom'])),$d['date_naissance']?:null,$d['sexe'],$d['type_patient'],$d['telephone'],$d['email'],$d['adresse'],$d['groupe_sanguin'],$id]);
    header('Location: index.php?msg=modifie');exit;
  }
}
$init=strtoupper(substr($d['prenom'],0,1).substr($d['nom'],0,1));
?>
<div class="page-header">
  <div><h1>Modifier un patient</h1><div class="breadcrumb"><a href="index.php">Patients</a> › Modifier</div></div>
  <a href="dossier.php?id=<?=$id?>" class="btn btn-outline">Voir le dossier</a>
</div>
<div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;padding:16px 20px;background:var(--bleu-clair);border-radius:var(--radius-lg);border:1px solid #bfdbfe">
  <div class="avatar av-blue" style="width:44px;height:44px;font-size:15px"><?=$init?></div>
  <div><div style="font-weight:700;font-size:15px;color:var(--bleu-fonce)"><?=htmlspecialchars($d['prenom'].' '.$d['nom'])?></div>
  <div style="font-size:12px;color:var(--gris-5)"><?=htmlspecialchars($d['matricule'])?> · Inscrit le <?=date('d/m/Y',strtotime($p['created_at']))?></div></div>
</div>
<?php if(!empty($err)):?><div class="alert alert-danger"><?=implode('<br>',$err)?></div><?php endif;?>
<div class="form-card">
  <div class="form-head"><h2>Informations du patient</h2></div>
  <form method="POST" class="form-body" id="fm">
    <div class="form-row">
      <div class="form-group"><label>Matricule <span class="req">*</span></label><input type="text" name="matricule" value="<?=htmlspecialchars($d['matricule'])?>" required></div>
      <div class="form-group"><label>Type</label><select name="type_patient"><option value="etudiant" <?=$d['type_patient']==='etudiant'?'selected':''?>>Étudiant(e)</option><option value="personnel" <?=$d['type_patient']==='personnel'?'selected':''?>>Personnel</option></select></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Prénom <span class="req">*</span></label><input type="text" name="prenom" value="<?=htmlspecialchars($d['prenom'])?>" required></div>
      <div class="form-group"><label>Nom <span class="req">*</span></label><input type="text" name="nom" value="<?=htmlspecialchars($d['nom'])?>" required></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Sexe</label><select name="sexe"><option value="M" <?=$d['sexe']==='M'?'selected':''?>>Masculin</option><option value="F" <?=$d['sexe']==='F'?'selected':''?>>Féminin</option></select></div>
      <div class="form-group"><label>Date de naissance</label><input type="date" name="date_naissance" value="<?=htmlspecialchars($d['date_naissance']??'')?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Téléphone</label><input type="text" name="telephone" value="<?=htmlspecialchars($d['telephone']??'')?>"></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" value="<?=htmlspecialchars($d['email']??'')?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Groupe sanguin</label><select name="groupe_sanguin"><option value="">— Non renseigné —</option><?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $g):?><option value="<?=$g?>" <?=$d['groupe_sanguin']===$g?'selected':''?>><?=$g?></option><?php endforeach;?></select></div>
      <div class="form-group"></div>
    </div>
    <div class="form-group"><label>Adresse</label><textarea name="adresse"><?=htmlspecialchars($d['adresse']??'')?></textarea></div>
  </form>
  <div class="form-foot">
    <button type="submit" form="fm" class="btn btn-primary">Enregistrer les modifications</button>
    <a href="index.php" class="btn btn-outline">Retour</a>
    <button style="margin-left:auto" class="btn btn-danger" onclick="confirmerSuppression('supprimer.php?id=<?=$id?>','<?=htmlspecialchars(addslashes($d['prenom'].' '.$d['nom']))?>')">Supprimer ce patient</button>
  </div>
</div>
<?php require_once '../includes/footer.php';?>
