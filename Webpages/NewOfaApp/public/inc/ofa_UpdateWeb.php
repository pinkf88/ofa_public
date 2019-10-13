<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$serien = explode("|", $_GET["serien"]);

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

for ($i = 0; $i < (count($serien) - 1) / 2; $i++)
{
    $sql = "UPDATE $dbt_ofa_web_serie ws SET nr=" . ($i+1) . " WHERE ws.webid=" . $serien[0] . " AND ws.serieid=" . $serien[2*$i+1];
    mysqli_query($db_link, $sql);
}

echo '{';
echo '"webid": "' . $serien[0] . '"';
echo '}';

mysqli_close($db_link);
?>
