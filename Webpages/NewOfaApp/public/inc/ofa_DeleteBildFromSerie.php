<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$serieid = 0 + $_GET['serieid'];
$bildid = 0 + $_GET['bildid'];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "DELETE FROM $dbt_ofa_serie_bild WHERE serieid=$serieid AND bildid=$bildid";
mysqli_query($db_link, $sql);
mysqli_close($db_link);
?>