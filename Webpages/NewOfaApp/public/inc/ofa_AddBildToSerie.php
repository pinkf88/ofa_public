<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$serieid = 0 + $_GET['serieid'];
$bildid = 0 + $_GET['bildid'];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "SELECT MAX(sb.nr) AS maxnr FROM $dbt_ofa_serie_bild sb WHERE sb.serieid=$serieid";
// echo $sql;

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

$sql = "INSERT $dbt_ofa_serie_bild VALUES (NULL, " . $serieid . ", " . $nr . ", " . $bildid . ", 8, \"\")";
// echo $sql;
mysqli_query($db_link, $sql);
mysqli_close($db_link);
?>
