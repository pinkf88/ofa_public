<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$bildnr = 0 + $_GET["bildnr"];
$polygon = $_GET["polygon"];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "UPDATE $dbt_ofa_bilddaten SET polygon=\"" . $polygon . "\" WHERE BildNr=$bildnr";

echo $sql;

mysqli_query($db_link, $sql);
mysqli_close($db_link);
?>
