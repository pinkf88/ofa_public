<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$key = $_GET["key"];
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

echo '{';
// echo '  "info": "' . $sql . ' ' . $key . ' ' . $value . '",';
echo '  "value": "' . $value . '"';
echo '}';

mysqli_close($db_link);
?>
