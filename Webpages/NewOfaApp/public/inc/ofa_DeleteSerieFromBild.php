<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$webid = 0 + $_GET['webid'];
$serieid = 0 + $_GET['serieid'];

ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "DELETE FROM $dbt_ofa_web_serie WHERE webid=$webid AND serieid=$serieid";
mysql_query($sql);
?>
