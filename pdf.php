<?php

require('fpdf/fpdf.php');
include("connexion.php");

$pdf = new FPDF();

$pdf->AddPage();

$pdf->SetFont('Arial','B',16);

$pdf->Cell(190,10,'Dossier Medical',0,1,'C');

$pdf->Ln(10);

$pdf->SetFont('Arial','',12);

$sql = "

SELECT
patients.nom,
patients.prenom,
consultations.date_consultation,
consultations.diagnostic,
consultations.traitement

FROM consultations

JOIN patients
ON consultations.patient_id = patients.id

";

$result = $conn->query($sql);

while($row = $result->fetch_assoc()){

$pdf->Cell(190,10,

$row['nom']." ".$row['prenom'].
" | ".$row['date_consultation'].
" | ".$row['diagnostic'].
" | ".$row['traitement']

,0,1);

}

$pdf->Output();

?>