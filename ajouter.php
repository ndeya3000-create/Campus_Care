<?php
require_once '../includes/db.php';
$page_title='Ajouter un patient';
require_once '../includes/header.php';
$err=[];$d=['matricule'=>'','nom'=>'','prenom'=>'','date_naissance'=>'','sexe'=>'M','type_patient'=>'etudiant','telephone'=>'','email'=>'','adresse'=>'','groupe_sanguin'=>''];
if($_SERVER['REQUEST_METHOD']==='POST'){
  foreach($d as $k=>$_){$d[$k]=trim($_POST[$k]??'');}
  if(!$d['matricule'])$err[]='Matricule obligatoire.';
  if(!$d['nom'])$err[]='Nom obligatoire.';
  if(!$d['prenom'])$err[]='Prénom obligatoire.';
  if(empty($err)){
    $ex=$pdo->prepare('SELECT id FROM t_patient WHERE matricule=?');$ex->execute([$d['matricule']]);
    if($ex->fetch())$err[]='Ce matricule existe déjà.';
  }
  if(empty($err)){
    $pdo->prepare('INSERT INTO t_patient(matricule,nom,prenom,date_naissance,sexe,type_patient,telephone,email,adresse,groupe_sanguin) VALUES(?,?,?,?,?,?,?,?,?,?)')->execute([$d['matricule'],strtoupper($d['nom']),ucfirst(strtolower($d['prenom'])),$d['date_naissance']?:null,$d['sexe'],$d['type_patient'],$d['telephone'],$d['email'],$d['adresse'],$d['groupe_sanguin']]);
    header('Location: index.php?msg=ajoute');exit;
  }
}
?>
<div class="page-header"><div><h1>Nouveau patient</h1><div class="breadcrumb"><a href="index.php">Patients</a> › Ajouter</div></div></div>
<?php if(!empty($err)):?><div class="alert alert-danger"><?=implode('<br>',$err)?></div><?php endif;?>
<div class="form-card">
  <div class="form-head"><svg width="18" height="18" fill="none" stroke="#1e3a5f" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg><h2>Informations du patient</h2></div>
  <form method="POST" class="form-body" id="f">
    <div class="form-row">
      <div class="form-group"><label>Matricule <span class="req">*</span></label><input type="text" name="matricule" value="<?=htmlspecialchars($d['matricule'])?>" placeholder="ETU-0001" required><div class="hint">ETU-XXXX pour étudiant, PER-XXXX pour personnel</div></div>
      <div class="form-group"><label>Type <span class="req">*</span></label><select name="type_patient"><option value="etudiant" <?=$d['type_patient']==='etudiant'?'selected':''?>>Étudiant(e)</option><option value="personnel" <?=$d['type_patient']==='personnel'?'selected':''?>>Personnel</option></select></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Prénom <span class="req">*</span></label><input type="text" name="prenom" value="<?=htmlspecialchars($d['prenom'])?>" required></div>
      <div class="form-group"><label>Nom <span class="req">*</span></label><input type="text" name="nom" value="<?=htmlspecialchars($d['nom'])?>" required></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Sexe</label><select name="sexe"><option value="M" <?=$d['sexe']==='M'?'selected':''?>>Masculin</option><option value="F" <?=$d['sexe']==='F'?'selected':''?>>Féminin</option></select></div>
      <div class="form-group"><label>Date de naissance</label><input type="date" name="date_naissance" value="<?=htmlspecialchars($d['date_naissance'])?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Téléphone</label><input type="text" name="telephone" value="<?=htmlspecialchars($d['telephone'])?>" placeholder="77 XXX XX XX"></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" value="<?=htmlspecialchars($d['email'])?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Groupe sanguin</label><select name="groupe_sanguin"><option value="">— Non renseigné —</option><?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $g):?><option value="<?=$g?>" <?=$d['groupe_sanguin']===$g?'selected':''?>><?=$g?></option><?php endforeach;?></select></div>
      <div class="form-group"></div>
    </div>
    <div class="form-group"><label>Adresse</label><textarea name="adresse"><?=htmlspecialchars($d['adresse'])?></textarea></div>
  </form>
  <div class="form-foot">
    <button type="submit" form="f" class="btn btn-success">Enregistrer le patient</button>
    <a href="index.php" class="btn btn-outline">Annuler</a>
  </div>
</div>
<?php require_once '../includes/footer.php';?>
