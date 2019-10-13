<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$webid = 0 + $_GET['webid'];
$serieid = 0 + $_GET['serieid'];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$titel = '';
$pfad = '';

$sql = "SELECT titel, pfad FROM $dbt_ofa_web_serie sb WHERE sb.webid=$webid AND sb.serieid=$serieid";

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows ($resultat) > 0)
    {
        $datensatz = mysqli_fetch_assoc ($resultat);

        $titel = $datensatz["titel"];
        $pfad = $datensatz["pfad"];
    }

    mysqli_free_result($resultat);
}

echo '{';
echo '"titel": "' . addslashes($titel) . '",';
echo '"pfad": "' . addslashes($pfad) . '"';
echo '}';

mysqli_close($db_link);
?>
