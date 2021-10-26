<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$trackid = $_GET["trackid"];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "SELECT t.title, t.album, t.duration, t.albumartist, t.artist, t.musicbrainz_albumid, t.year, t.originalyear, t.musicbrainz_recordingid, t.pic, t.musicbrainz_trackid "
    . "FROM $dbt_ofa_tracks t WHERE t.musicbrainz_trackid=\"$trackid\"";
// echo $sql;
// SELECT musicbrainz_albumid, albumartist, album, year, originalyear, COUNT(musicbrainz_recordingid) FROM `ofa_tracks` GROUP BY musicbrainz_albumid, albumartist, album, year, originalyear ORDER BY albumartist, album

$albumartist = '';
$artist = '';
$musicbrainz_albumid = '';
$musicbrainz_recordingid = '';
$year = 0;
$originalyear = 0;
$pic = 0;
$title = '';
$album = '';
$duration = '';

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows ($resultat) > 0)
    {
        $datensatz = mysqli_fetch_assoc ($resultat);
        $title = $datensatz["title"];
        $album = $datensatz["album"];
        $duration = $datensatz["duration"];
        $albumartist = $datensatz["albumartist"];
        $artist = $datensatz["artist"];
        $musicbrainz_albumid = $datensatz["musicbrainz_albumid"];
        $musicbrainz_recordingid = $datensatz["musicbrainz_recordingid"];
        $year = 0 + $datensatz["year"];
        $originalyear = 0 + $datensatz["originalyear"];
        $pic = $datensatz["pic"];
    }

    mysqli_free_result($resultat);
}

if ($artist == 'AC') {
    $artist = $albumartist;
}

$bilddaten = '';
$bildurl = '';

if ($pic == 1 && $albumartist != '') {
    $albumartist_dir = preg_replace("/\./", "", preg_replace("/'/", "", preg_replace("/!/", "", preg_replace("/\//", "", preg_replace("/ /", "_", strtolower($albumartist))))));

    $bilddaten = '<a class=\"fancybox\" rel=\"group\" href=\"/covers/' . $albumartist_dir . '/' . $musicbrainz_albumid . '.jpg\">'
        . '<img class=\"mini\" src=\"/covers/' . $albumartist_dir . '/' . $musicbrainz_albumid . '.jpg\"></a><br>';

    $bildurl = '/covers/' . $albumartist_dir . '/' . $musicbrainz_albumid . '.jpg';
}

$trackdaten = '';

$trackdaten .= '<p class=\"title\"><b>' . preg_replace('/"/', '\"', $title) . '</b></p>';
$trackdaten .= '<p class=\"artist\">' . preg_replace('/"/', '\"', $artist) . '</p>';
$trackdaten .= '<p class=\"album\"><i>' . preg_replace('/"/', '\"', $album) . '</i></p>';
$trackdaten .= '<p class=\"duration\">' . $duration . '</p>';

if ($year > 0) {
    $trackdaten .= '<p class=\"year\">' . $year;

    if ($originalyear > 0 && $year != $originalyear) {
        $trackdaten .= ' (' . $originalyear . ')';
    }

    $trackdaten .= '</p>';
}

$trackdaten .= '<p class=\"mblink\"><a href=\"https://musicbrainz.org/track/' . $trackid . '\" target=\"_blank\">MusicBrainz</a></p>';
$trackdata = '{ "trackid": "' . $trackid . '", "title": "' . preg_replace('/"/', '\"', $title) . '", "bildurl": "' . $bildurl . '" }';

echo '{';
echo '  "bilddaten": "' . $bilddaten . '",';
echo '  "trackdaten": "' . $trackdaten . '",';
echo '  "trackdata": ' . $trackdata;
echo '}';

mysqli_close($db_link);
?>
