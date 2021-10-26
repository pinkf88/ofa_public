<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$id = $_GET["id"];
$compilation = 0 + $_GET["compilation"];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = 'UPDATE ofa_tracks SET compilation=' . $compilation . ' WHERE musicbrainz_albumid="' . $id . '"';

// echo $sql;

mysqli_query($db_link, $sql);
mysqli_close($db_link);
?>
