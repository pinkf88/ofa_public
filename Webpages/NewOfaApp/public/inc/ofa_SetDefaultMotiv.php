<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$bildid = 0 + $_GET['bildid'];
$motivid = 0 + $_GET['motivid'];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "SELECT bm.motivid, bm.default FROM $dbt_ofa_bild_motiv bm WHERE bm.bildid=$bildid";
// echo $sql;

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows($resultat) > 0)
    {
        while ($datensatz = mysqli_fetch_assoc($resultat))
        {
            if ($datensatz["motivid"] == $motivid)
            {
                $sql = "UPDATE $dbt_ofa_bild_motiv bm SET bm.default=1 WHERE bm.bildid=$bildid AND bm.motivid=" . $datensatz["motivid"];
            }
            else
            {
                $sql = "UPDATE $dbt_ofa_bild_motiv bm SET bm.default=0 WHERE bm.bildid=$bildid AND bm.motivid=" . $datensatz["motivid"];
            }

            // echo $sql;
            mysqli_query($db_link, $sql);
        }
    }

    mysqli_free_result($resultat);
}

mysqli_close($db_link);

// echo 'Motive: ' . $_GET['motive'];
// print_r ($motive);
?>