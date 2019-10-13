<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

// var_dump($_POST);

$serieid =  $_GET["serieid"];
$bilder = explode("|", $_POST["bilder"]);

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

for ($i = 0; $i < count($bilder); $i++)
{
    $sql = "UPDATE $dbt_ofa_serie_bild sb SET nr=" . ($i+1) . " WHERE sb.serieid=" . $serieid . " AND sb.bildid=" . $bilder[$i];
    mysqli_query($db_link, $sql);
}

echo '{';
echo '"serieid": "' . $serieid . '"';
echo '}';

mysqli_close($db_link);
?>
