<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$albumid = $_GET['albumid'];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = 'UPDATE ofa_tracks SET pic="1" WHERE musicbrainz_albumid="' . $albumid . '"';

mysqli_query($db_link, $sql);
mysqli_close($db_link);
?>
