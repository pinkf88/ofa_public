<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$webid = 0 + $_GET['webid'];
$serieid = 0 + $_GET['serieid'];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "SELECT serieid, nr FROM $dbt_ofa_web_serie WHERE webid=$webid ORDER BY nr";

if ($resultat = mysqli_query($db_link, $sql))
{
    $nr = 0;

    if (mysqli_num_rows($resultat) > 0)
    {
        while ($datensatz = mysqli_fetch_assoc($resultat))
        {
            if ($datensatz["serieid"] == $serieid)
            {
                $nr = $datensatz["nr"];
            }
            
            if ($nr > 0)
            {
                $sql = "UPDATE $dbt_ofa_web_serie SET nr=" . ($datensatz["nr"] - 1) . " WHERE webid=$webid AND serieid=" . $datensatz["serieid"];
                mysqli_query($db_link, $sql);
                echo $sql;
            }
        }
    }

    mysqli_free_result($resultat);
}

$sql = "DELETE FROM $dbt_ofa_web_serie WHERE webid=$webid AND serieid=$serieid";
mysqli_query($db_link, $sql);
mysqli_close($db_link);
?>
