<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$serieid = 0 + $_GET['serieid'];
$bildid = 0 + $_GET['bildid'];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$zusatz = '';

$sql = "SELECT zusatz FROM $dbt_ofa_serie_bild sb WHERE sb.serieid=$serieid AND sb.bildid=$bildid";

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows ($resultat) > 0)
    {
        $datensatz = mysqli_fetch_assoc ($resultat);
        $zusatz = $datensatz["zusatz"];
    }

    mysqli_free_result($resultat);
}

echo $zusatz;

mysqli_close($db_link);
?>
