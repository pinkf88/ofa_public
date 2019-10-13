<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$key = $_GET["key"];
$value = $_GET["value"];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "UPDATE $dbt_ofa_info SET infovalue=\"$value\" WHERE infokey=\"$key\"";
mysqli_query($db_link, $sql);

echo $sql . ' ' . $key . ' ' . $value;

mysqli_close($db_link);
?>
