<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$bildid = 0 + $_GET['bildid'];
$wertung = 0 + $_GET['wertung'];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "UPDATE $dbt_ofa_bild b SET b.wertung=\"$wertung\" WHERE b.id=$bildid";
// echo $sql;
mysqli_query($db_link, $sql);

echo '{';
echo '  "bildid": ' . $bildid . ',';
echo '  "wertung": ' . $wertung;
echo '}';

mysqli_close($db_link);
?>
