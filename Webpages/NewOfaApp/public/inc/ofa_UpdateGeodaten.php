<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$datei = 0 + $_GET['datei'];
$breite = 0 + $_GET['breite'];
$laenge = 0 + $_GET['laenge'];
$hoehe = 0 + $_GET['hoehe'];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "UPDATE $dbt_ofa_bilddaten bd SET bd.Laenge=$laenge, bd.Breite=$breite, bd.Hoehe=$hoehe WHERE bd.BildNr=$datei";
// echo $sql;
mysqli_query($db_link, $sql);
mysqli_close($db_link);
?>