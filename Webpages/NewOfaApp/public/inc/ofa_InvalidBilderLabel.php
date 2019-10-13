<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$labelid = 0 + $_GET['labelid'];
$serieid = 0 + $_GET["serieid"];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "UPDATE $dbt_ofa_bild_label bl INNER JOIN $dbt_ofa_serie_bild sb ON (bl.bildid=sb.bildid) SET valid=0 "
    . "WHERE sb.serieid=$serieid AND bl.labelid=$labelid";

// echo $sql;
mysqli_query($db_link, $sql);

$sql = "SELECT DISTINCT label_en, label_de "
    . "FROM $dbt_ofa_label "
    . "WHERE id=$labelid";

// echo $sql;

if ($resultat_label = mysqli_query($db_link, $sql))
{
    $datensatz_label = mysqli_fetch_assoc($resultat_label);
    echo $datensatz_label['label_de'] . ' / ' . $datensatz_label['label_en'];

    mysqli_free_result($resultat_label);
}

mysqli_close($db_link);
?>
