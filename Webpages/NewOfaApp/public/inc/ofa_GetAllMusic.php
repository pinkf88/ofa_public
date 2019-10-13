<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = 'SELECT DISTINCT t.id, t.musicbrainz_trackid, t.albumartist, t.albumartistsort, t.album, t.discnumber, t.totaldiscs, t.musicbrainz_albumid, t.year, t.originalyear, t.track, t.title, t.duration, t.genre '
    . 'FROM ' . $dbt_ofa_tracks . ' t ORDER BY t.albumartistsort, t.albumartist, t.album, t.year, t.musicbrainz_albumid, t.discnumber, t.track';
// echo $sql;

$albumartist = '';
$musicbrainz_albumid = '';
$new_artist = 0;
$new_musicbrainz_albumid = 0;
$first_track = 0;

echo "[\n";


if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows ($resultat) > 0)
    {
        while ($datensatz = mysqli_fetch_assoc ($resultat)) {
            if ($albumartist != $datensatz["albumartist"]) {
                if ($new_artist == 1) {
                    echo "\n          ]\n";
                    echo "        }\n";
                    echo "      }\n";
                    echo "    ]\n";
                }

                if ($albumartist != '') {
                    echo "  },\n";
                }

                $albumartist = $datensatz["albumartist"];
                echo "  {\n";
                echo '    "' . preg_replace( '/"/', '\"', $albumartist) . '|' . preg_replace( '/"/', '\"', $datensatz["albumartistsort"]) . '":' . "\n    [\n";
                $new_artist = 1;
                $new_musicbrainz_albumid = 0;
            }

            /*
            if ($new_artist == 1) {
                $new_artist = 0;
            } else {
                echo "XX,\n";
            }
*/
            if ($musicbrainz_albumid != $datensatz["musicbrainz_albumid"]) {
                if ($new_musicbrainz_albumid == 1) {
                    echo "\n          ]\n";
                    echo "        }\n";
                    echo "      },\n";
                }

                $musicbrainz_albumid = $datensatz["musicbrainz_albumid"];

                echo "      {\n";
                echo '        "' . preg_replace( '/"/', '\"', $datensatz["album"]) . '":' . "\n";
                echo "        {\n";
                echo '          "musicbrainz_albumid": "' . $datensatz["musicbrainz_albumid"] . '",' . "\n";
                echo '          "year": "' . $datensatz["year"] . '",' . "\n";
                echo '          "originalyear": "' . $datensatz["originalyear"] . '",' . "\n";
                echo '          "tracks" :' . "\n          [\n";
                $new_musicbrainz_albumid = 1;
                $first_track = 1;
            }

            if ($first_track == 1) {
                $first_track = 0;
            } else {
                echo ",\n";
            }

            echo '            { "id": "' . $datensatz["id"] . '", "trackid": "' . $datensatz["musicbrainz_trackid"] . '", "track": "' . $datensatz["track"] . '", "title": "' . preg_replace( '/"/', '\"', $datensatz["title"]) . '", "duration": "' . $datensatz["duration"] . '", "genre": "' . $datensatz["genre"] . '" }';
        }
    }

    mysqli_free_result($resultat);
}

echo "\n          ]\n";
echo "        }\n";
echo "      }\n";
echo "    ]\n";
echo "  }\n";
echo ']';

mysqli_close($db_link);
?>
