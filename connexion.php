<?php

$conn = new mysqli("localhost","root","","centre_de_sante");

if($conn->connect_error){
die("Erreur connexion");
}

?>