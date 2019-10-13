<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$webid = 0 + $_GET['webid'];
$serieid = 0 + $_GET['serieid'];
$titel = $_GET['titel'];
$pfad = $_GET['pfad'];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "SELECT MAX(ws.nr) AS maxnr FROM $dbt_ofa_web_serie ws WHERE ws.webid=$webid";
// echo 'SQL 1: |' . $sql . '|';

$nr = 1;

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows ($resultat) > 0)
    {
        $datensatz = mysqli_fetch_assoc ($resultat);

        $nr = $datensatz["maxnr"] + 1;
    }

    mysqli_free_result($resultat);
}

$sql = "INSERT $dbt_ofa_web_serie VALUES (NULL, " . $webid . ", " . $nr . ", " . $serieid . ", \"" . $titel . "\", \"" . $pfad . "\")";
// echo 'SQL 2: |' . $sql . '|';
mysqli_query($db_link, $sql);

mysqli_close($db_link);
?>