<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$id = $_GET["id"];
$laenge = $_GET["laenge"];
$breite = $_GET["breite"];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "UPDATE $dbt_ofa_ort SET laenge=" . $laenge . ", breite=" . $breite . " WHERE id=" . $id;
mysqli_query($db_link, $sql);

mysqli_close($db_link);
?>
