<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$artistid = $_GET["artistid"];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = 'SELECT DISTINCT t.album, t.musicbrainz_albumid, t.year, t.originalyear, COUNT(t.id) AS anzahl, t.albumartist, t.pic '
    . 'FROM ' . $dbt_ofa_tracks . ' t WHERE t.musicbrainz_albumartistid="' . $artistid . '" GROUP BY t.musicbrainz_albumid ORDER BY t.originalyear, t.album, t.year, t.musicbrainz_albumid';

$resultat = mysqli_query($db_link, $sql);
// print_r($resultat);

$albumdaten = '<div class="albumgrid_bottom"></div><ul id="albumgrid">';

while ($datensatz = mysqli_fetch_assoc ($resultat)) {
    $albumid = $datensatz["musicbrainz_albumid"];
    $bilddaten = '';

    if ($datensatz["pic"] == 1) {
        $albumartist = preg_replace("/\./", "", preg_replace("/'/", "", preg_replace("/!/", "", preg_replace("/\//", "", preg_replace("/ /", "_", strtolower($datensatz["albumartist"]))))));

        $bilddaten = '<a href="javascript:album_playAlbum(\'' . $albumid . '\', 1);">'
            . '<img class="mini" src="/covers/' . $albumartist . '/' . $albumid . '.jpg"></a>';
    }

    $jahr = '';
    
    if ($datensatz["year"] != $datensatz["originalyear"]) {
        $jahr = ' (' . $datensatz["year"] . ')';
    }
    
    $albumdaten .= '<li id="id' . $datensatz["bildid"] . '" class="ui-state-default">'
        . '<div class="albumgrid"><div class="albumgrid_top">'
        . '<a style="color: #2a8af2;" href="javascript:album_playAlbum(\'' . $albumid . '\', 1);">' . $datensatz["originalyear"] . '&nbsp;&nbsp;&nbsp;<b>' .$datensatz["album"] . '</b>' . $jahr . '</a></div>'
        . '<div>' . $bilddaten . '</div>'
        . '<div class="albumgrid_select">'
        . '<a style="color: #2a8af2;" href="javascript:album_playAlbum(\'' . $albumid . '\', 1);">Play</a> | '
        . '<a style="color: #2a8af2;" href="javascript:album_playAlbum(\'' . $albumid . '\', 2);">Play random</a> | '
        . '<a style="color: #2a8af2;" href="javascript:control_addToRunningTracks(1, \'' . $albumid . '\');">Add</a>'
        . '</div></div>'
        . '</li>' . "\n";
}

$albumdaten .= '</ul><div class="albumgrid_bottom"></div>' . "\n";

mysqli_free_result($resultat);

echo $albumdaten;

mysqli_close($db_link);
?>
