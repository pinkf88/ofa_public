<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$bildid = 0 + $_GET["bildid"];

$key = 'bildinfo';
$value = '';

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "SELECT infovalue FROM $dbt_ofa_info WHERE infokey=\"$key\"";

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows ($resultat) > 0)
    {
        $datensatz = mysqli_fetch_assoc ($resultat);
        $value = $datensatz["infovalue"];
    }

    mysqli_free_result($resultat);
}

$sql = "UPDATE $dbt_ofa_bild SET info=\"" . $value . "\" WHERE id=$bildid";
// echo $sql;

mysqli_query($db_link, $sql);
mysqli_close($db_link);
?>
