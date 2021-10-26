<?php
include_once "./ofa_DbConsts.php";
include_once "./ofa_Database.php";

$no_letters = [
    '"',
    '#',
    '\'',
    '(',
    '-',
    '.',
    '['
];

$html_tracks = '<div id="tracks_sort_track_intro" class="tracks_sort_track_intro">' . "\n";

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = 'SELECT DISTINCT UPPER(LEFT(t.title, 1)) AS firstletter '
    . 'FROM ' . $dbt_ofa_tracks . ' t ORDER BY UPPER(LEFT(t.title, 1))';

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows ($resultat) > 0)
    {
        $letters = "| ";

        while ($datensatz = mysqli_fetch_assoc ($resultat)) {
            if (in_array($datensatz["firstletter"], $no_letters) == false) {
                $letters .= "<a href=\"javascript:control_scrollToTrackLetter('" . $datensatz["firstletter"] . "')\">" . $datensatz["firstletter"] . "</a> | ";
            }
        }

        $html_tracks .= '<div id="tracks_sort_track_intro_firstletters" class="tracks_sort_track_intro_firstletters">' . $letters . '</div>' . "\n";
    }
}

$html_tracks .= '<div id="media_tracks_intro_search" class="media_tracks_intro_search">' . "\n";
$html_tracks .= '<input type="text" id="track_search" class="track_search">' . "\n";
$html_tracks .= '</div>' . "\n";
$html_tracks .= '</div>' . "\n";

$sql = 'SELECT DISTINCT t.title, t.album, t.albumartist, t.albumartistsort, t.musicbrainz_albumartistid, UPPER(LEFT(t.title, 1)) AS firstletter, '
    . 't.musicbrainz_trackid, t.year, t.originalyear, t.studio, t.duration, t.count_play '
    . 'FROM ' . $dbt_ofa_tracks . ' t '
    . 'ORDER BY t.title, t.albumartistsort, t.albumartist, t.studio DESC, t.originalyear, t.year, t.album, t.musicbrainz_trackid';

function getHtmlMediaTrack($datensatz)
{
    $jahr = "";
    $originalyear = $datensatz["originalyear"];

    if ($datensatz["originalyear"] == 0) {
        $originalyear = $datensatz["year"];
    }

    if ($datensatz["year"] != $datensatz["originalyear"]) {
        $jahr = " | "  . $datensatz["year"];
    }

    $track_id = preg_replace( "/\"|\'|-/", "", strtolower($datensatz["title"] . $datensatz["albumartist"] . $datensatz["album"]));

    $html = "<div id=\"" . $track_id . "\" class=\"media_track\">"
        . "<p><a href=\"javascript:album_playTrack('" . $datensatz["musicbrainz_trackid"] . "', 0);\"><span class=\"ui-icon ui-icon-play\"></span></a> | "
        . "<a href=\"javascript:control_addToRunningTracks(2, '" . $datensatz["musicbrainz_trackid"] . "');\"><span class=\"ui-icon ui-icon-plusthick\"></span></a>&nbsp;&nbsp;&nbsp;"
        . "<a href=\"javascript:album_playTrack('" . $datensatz["musicbrainz_trackid"] . "', 0);\">";

    if ($datensatz["studio"] == 1) {
        $html .= "<b>" . $datensatz["title"] . "</b>";
    } else  {
        $html .= "<b><i>" . $datensatz["title"] . "</i></b>";
    }
    
    $html .= " | " . $datensatz["albumartist"] . " | " . $originalyear . " | " . $datensatz["duration"]
        . " | " . $datensatz["count_play"] . " mal gespielt | <i>" . $datensatz["album"] . "</i>" . $jahr . "</a></p></div>\n";

    return $html;
}
    
if ($resultat = mysqli_query($db_link, $sql)) {
    if (mysqli_num_rows ($resultat) > 0) {
        $firstletter = '';

        while ($datensatz = mysqli_fetch_assoc ($resultat)) {
            if ($firstletter != $datensatz["firstletter"]) {
                $firstletter = $datensatz["firstletter"];

                if (in_array($datensatz["firstletter"], $no_letters) == false) {
                    $html_tracks .= '<p id="track_' . $firstletter . '" class="media_track_firstletter">' . $firstletter;
                    $html_tracks .= ' <a href="javascript:control_scrollToTrackLetter();"><span class="ui-icon ui-icon-arrowthick-1-n"></span></a>';
                    $html_tracks .= '</p>' . "\n";
                }
            }

            $html_tracks .= getHtmlMediaTrack($datensatz);
        }
    }

    mysqli_free_result($resultat);
}

mysqli_close($db_link);

echo $html_tracks;
?>
