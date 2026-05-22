<?php
include("connexion.php");

$result = $conn->query("
SELECT patients.nom, patients.prenom, consultations.*
FROM consultations
JOIN patients ON consultations.patient_id = patients.id
");
?>

<!DOCTYPE html>
<html>

<head>

<title>Dossier médical</title>
<link rel="stylesheet" href="style.css">

</head>

<body>

<h1>Dossier Médical des Patients</h1>

<input type="text" id="search" placeholder="Rechercher patient">

<table id="table">

<tr>
<th>Patient</th>
<th>Date</th>
<th>Symptômes</th>
<th>Diagnostic</th>
<th>Traitement</th>
</tr>

<?php while($row = $result->fetch_assoc()){ ?>

<tr>

<td><?= $row['nom']." ".$row['prenom'] ?></td>
<td><?= $row['date_consultation'] ?></td>
<td><?= $row['symptomes'] ?></td>
<td><?= $row['diagnostic'] ?></td>
<td><?= $row['traitement'] ?></td>

</tr>

<?php } ?>

</table>

<script src="script.js"></script>

</body>
</html>
