<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$webid = 0 + $_GET['webid'];
$serieid = 0 + $_GET['serieid'];
$titel = $_GET['titel'];
$pfad = $_GET['pfad'];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "UPDATE $dbt_ofa_web_serie ws SET ws.titel=\"" . addslashes($titel) . "\", ws.pfad=\"" . addslashes($pfad) . "\" WHERE ws.webid=$webid AND ws.serieid=$serieid";
mysqli_query($db_link, $sql);

echo '{';
echo '"zusatz": "' . $sql . '"';
echo '}';

mysqli_close($db_link);
?>
