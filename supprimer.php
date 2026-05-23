<?php
require_once '../includes/db.php';
if(session_status()===PHP_SESSION_NONE)session_start();
if(!isset($_SESSION['user'])){header('Location: /campuscare/login.php');exit;}
$id=(int)($_GET['id']??0);
if($id){$pdo->prepare('DELETE FROM t_patient WHERE id=?')->execute([$id]);}
header('Location: index.php?msg=supprime');exit;
