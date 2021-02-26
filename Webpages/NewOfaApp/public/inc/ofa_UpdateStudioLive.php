<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$id = $_GET["id"];
$studio = 0 + $_GET["studio"];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = '';

if ($studio < 10) {
    $sql = 'UPDATE ofa_tracks SET studio=' . $studio . ' WHERE musicbrainz_albumid="' . $id . '"';
} else {
    $sql = 'UPDATE ofa_tracks SET studio=' . ($studio - 10) . ' WHERE musicbrainz_trackid="' . $id . '"';
}

// echo $sql;

mysqli_query($db_link, $sql);

mysqli_close($db_link);
?>
