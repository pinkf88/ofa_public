<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$albumid = $_GET["albumid"];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "SELECT DISTINCT t.albumartist, t.album, t.discnumber, t.totaldiscs, t.discsubtitle, t.year, t.originalyear, t.pic, t.path, "
    . "a.discogs_albumid, a.genres, a.styles "
    . "FROM ofa_tracks t LEFT JOIN ofa_albums a ON (t.musicbrainz_albumid = a.musicbrainz_albumid)"
    . "WHERE t.musicbrainz_albumid=\"" . $albumid . "\" ORDER BY t.discnumber";
// SELECT musicbrainz_albumid, albumartist, album, year, originalyear, COUNT(musicbrainz_recordingid) FROM `ofa_tracks` GROUP BY musicbrainz_albumid, albumartist, album, year, originalyear ORDER BY albumartist, album
// echo $sql;

$albumartist = '';
$album = '';
$discsubtitle = '';
$totaldiscs = 0;
$year = 0;
$originalyear = 0;
$pic = 0;
$path = '';

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows ($resultat) > 0)
    {
        $datensatz = mysqli_fetch_assoc ($resultat);
        $albumartist = preg_replace('/"/', '\"', $datensatz["albumartist"]);
        $album = preg_replace('/"/', '\"', $datensatz["album"]);

        if ($datensatz["discsubtitle"] != '') {
            $discsubtitle .= $datensatz["discnumber"] . ': ' . $datensatz["discsubtitle"] . ' | ';
        }

        $totaldiscs = $datensatz["totaldiscs"];
        $year = $datensatz["year"];
        $originalyear = $datensatz["originalyear"];
        $pic = $datensatz["pic"];
        // $path = $datensatz["path"]; // preg_replace('/\/', '\\', $datensatz["path"]);
    }

    mysqli_free_result($resultat);
}

$albumdaten = '<p class=\"album\"><b>' . $album . '<br><i>' . $albumartist . '</i></b></p>';

if ($discsubtitle != '') {
    $albumdaten .= '<p class=\"discsubtitle\"><i>| ' . preg_replace('/"/', '\"', $discsubtitle) . '</i></p>';
}

if ($totaldiscs > 1) {
    $albumdaten .= '<p class=\"totaldiscs\">' . $totaldiscs . ' Discs</p>';
}

$jahr = '';

if ($year > 0) {
	$jahr = '' . $year . ' ';
}

if ($originalyear > 0 && $originalyear != $year) {
	$jahr .= '(' . $originalyear . ')';
}

$albumdaten .= '<p class=\"year\">' . $jahr . '</p>';
$albumdaten .= '<p class=\"path\">' . $path . '</p>';

if (strlen($albumid) == 36) {
    $albumdaten .= '<p class=\"rightside_mblink\"><a href=\"https://musicbrainz.org/release/' . $albumid . '\" target=\"_blank\">MusicBrainz</a></p>';
}

$albumdaten .= '<p class=\"albumid\">' . $albumid . '</p>';

if (is_null($datensatz["discogs_albumid"]) == FALSE) {
    $albumdaten .= '<p class=\"rightside_discogs_albumid\">' . $datensatz["discogs_albumid"] . '</p>';
}

if (is_null($datensatz["genres"]) == FALSE && $datensatz["genres"] != '') {
    $genres = preg_replace("/\|/", " | ", trim($datensatz["genres"], "|"));
    $albumdaten .= '<p class=\"rightside_genres\">Genres: ' . $genres . '</p>';
}

if (is_null($datensatz["styles"]) == FALSE && $datensatz["styles"] != '') {
    $styles = preg_replace("/\|/", " | ", trim($datensatz["styles"], "|"));
    $albumdaten .= '<p class=\"rightside_styles\">Styles: ' . $styles . '</p>';
}

$bilddaten = '';

if ($pic == 1 && $albumartist != '') {
    $albumartist = preg_replace("/\./", "", preg_replace("/'/", "", preg_replace("/!/", "", preg_replace("/\//", "", preg_replace("/ /", "_", strtolower($albumartist))))));

    $bilddaten = '<a class=\"fancybox\" rel=\"group\" href=\"/covers/' . $albumartist . '/' . $albumid . '.jpg\">'
        . '<img class=\"mini\" src=\"/covers/' . $albumartist . '/' . $albumid . '.jpg\"></a><br>';
}

$tracksdata = '';

$sql = "SELECT DISTINCT t.musicbrainz_trackid, t.track, t.title, t.genre "
    . "FROM $dbt_ofa_tracks t WHERE t.musicbrainz_albumid=\"" . $albumid . "\" ORDER BY t.discnumber, t.track";
// SELECT musicbrainz_albumid, albumartist, album, year, originalyear, COUNT(musicbrainz_recordingid) FROM `ofa_tracks` GROUP BY musicbrainz_albumid, albumartist, album, year, originalyear ORDER BY albumartist, album
// echo $sql;

$first_track = true;

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows ($resultat) > 0)
    {
        while ($datensatz = mysqli_fetch_assoc ($resultat)) {
            if ($first_track == true) {
                $first_track = false;
            } else {
                $tracksdata .= ', ';
            }

            $tracksdata .= '{ '
                . '"trackid": "' . $datensatz["musicbrainz_trackid"] . '", '
                . '"track": "' . $datensatz["track"] . '", '
                . '"title": "' . preg_replace( '/"/', '\"', $datensatz["title"]) . '", '
                . '"genre": "' . $datensatz["genre"] . '" '
                . '}';
        }
    }

    mysqli_free_result($resultat);
}

echo '{';
echo '  "bilddaten": "' . $bilddaten . '",';
echo '  "albumdaten": "' . $albumdaten . '",';
echo '  "tracksdata": [' . $tracksdata . ']';
echo '}';

mysqli_close($db_link);
?>
