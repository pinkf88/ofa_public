<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$bildlabelid = 0 + $_GET['bildlabelid'];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "UPDATE $dbt_ofa_bild_label SET valid=1 WHERE id=$bildlabelid";
// echo $sql;
mysqli_query($db_link, $sql);

$sql = "SELECT DISTINCT lb.label_en, lb.label_de, bl.score "
    . "FROM $dbt_ofa_bild_label bl, $dbt_ofa_label lb "
    . "WHERE bl.labelid=lb.id AND bl.id=$bildlabelid";

// echo $sql;

if ($resultat_label = mysqli_query($db_link, $sql))
{
    $datensatz_label = mysqli_fetch_assoc($resultat_label);
    echo '<a href="javascript:serie_validLabel(' . $bildlabelid . ')">' . $datensatz_label['label_de'] . ' / ' . $datensatz_label['label_en'] . ' (' . $datensatz_label['score'] . ')</a>';

    mysqli_free_result($resultat_label);
}

mysqli_close($db_link);
?>
