<?php
include("connexion.php");

/* HISTORIQUE CONSULTATIONS + PRESCRIPTIONS */

$sql = "

SELECT
patients.nom,
patients.prenom,
consultations.id,
consultations.date_consultation,
consultations.symptomes,
consultations.diagnostic,
consultations.traitement,
prescriptions.medicament,
prescriptions.dosage,
prescriptions.duree

FROM consultations

JOIN patients
ON consultations.patient_id = patients.id

LEFT JOIN prescriptions
ON prescriptions.consultation_id = consultations.id

ORDER BY consultations.date_consultation DESC

";

$result = $conn->query($sql);


/* SUIVI TRAITEMENTS */

$traitements = $conn->query("

SELECT
patients.nom,
patients.prenom,
traitements.medicament,
traitements.date_debut,
traitements.date_fin,
traitements.etat,
traitements.observations

FROM traitements

JOIN patients
ON traitements.patient_id = patients.id

");

?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<title>Dossier Médical</title>

<link rel="stylesheet" href="dossier_medical.css">
</head>

<body>

<h1>Dossier Médical des Patients</h1>

<input type="text" id="search" placeholder="Rechercher un patient...">

<!-- HISTORIQUE -->

<h2>Historique des Consultations</h2>

<table id="table">

<tr>
<th>Patient</th>
<th>Date</th>
<th>Symptômes</th>
<th>Diagnostic</th>
<th>Traitement</th>
<th>Médicament</th>
<th>Dosage</th>
<th>Durée</th>
</tr>

<?php while($row = $result->fetch_assoc()){ ?>

<tr>
<td><?= $row['nom']." ".$row['prenom'] ?></td>
<td><?= $row['date_consultation'] ?></td>
<td><?= $row['symptomes'] ?></td>
<td><?= $row['diagnostic'] ?></td>
<td><?= $row['traitement'] ?></td>
<td><?= $row['medicament'] ?></td>
<td><?= $row['dosage'] ?></td>
<td><?= $row['duree'] ?></td>
</tr>

<?php } ?>

</table>


<!-- SUIVI TRAITEMENTS -->

<h2>Suivi des Traitements</h2>

<table>

<tr>
<th>Patient</th>
<th>Médicament</th>
<th>Date début</th>
<th>Date fin</th>
<th>État</th>
<th>Observations</th>
</tr>

<?php while($t = $traitements->fetch_assoc()){ ?>

<tr>
<td><?= $t['nom']." ".$t['prenom'] ?></td>
<td><?= $t['medicament'] ?></td>
<td><?= $t['date_debut'] ?></td>
<td><?= $t['date_fin'] ?></td>
<td>
<?php
if($t['etat']=="Terminé"){
echo "<span class='termine'>Terminé</span>";
}else{
echo "<span class='encours'>En cours</span>";
}
?>
</td>
<td><?= $t['observations'] ?></td>
</tr>

<?php } ?>

</table>


<!-- BOUTONS -->

<div class="buttons">

<button onclick="window.print()" class="print-btn">
Imprimer le dossier
</button>

<a href="pdf.php" class="pdf-btn">
Télécharger PDF
</a>

</div>


<!-- SCRIPT RECHERCHE -->

<script>

document.getElementById("search").addEventListener("keyup", function(){

let filter = this.value.toLowerCase();

let rows = document.querySelectorAll("#table tr");

rows.forEach((row,i)=>{

if(i===0) return;

let text = row.innerText.toLowerCase();

row.style.display = text.includes(filter) ? "" : "none";

});

});

</script>

</body>
</html>