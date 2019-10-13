<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$serieid = 0 + $_GET['serieid'];
$bildid = 0 + $_GET['bildid'];
$dauer = 0 + $_GET['dauer'];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "UPDATE $dbt_ofa_serie_bild sb SET sb.dauer=\"$dauer\" WHERE sb.serieid=$serieid AND sb.bildid=$bildid";
mysqli_query($db_link, $sql);

echo '{';
echo '  "serieid": ' . $serieid . ',';
echo '  "bildid": ' . $bildid . ',';
echo '  "dauer": ' . $dauer;
echo '}';
    
mysqli_close($db_link);
?>
