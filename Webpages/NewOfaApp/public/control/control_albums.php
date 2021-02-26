<div id="control_tab_albums">
<h4 class="control">ALBEN</h4>
<h5 class="control" >Sortiert nach
<span id="sort_artist">Künstler</span> |
<span id="sort_year"><a href="javascript:control_sortYear()">Jahr</a></span>
</h5>
<hr class="control">
<div id="alben_sort_artist">
<div id="alben_sort_artist_intro" class="alben_sort_artist_intro">
<?php
$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = 'SELECT DISTINCT UPPER(LEFT(t.albumartistsort, 1)) AS firstletter '
    . 'FROM ' . $dbt_ofa_tracks . ' t ORDER BY UPPER(LEFT(t.albumartistsort, 1))';

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows ($resultat) > 0)
    {
        $letters = "| ";

        while ($datensatz = mysqli_fetch_assoc ($resultat)) {
            $letters .= "<a href=\"javascript:control_scrollToLetter('" . $datensatz["firstletter"] . "')\">" . $datensatz["firstletter"] . "</a> | ";
        }

        echo '<div id="alben_sort_artist_intro_firstletters" class="alben_sort_artist_intro_firstletters">' . $letters . '</div>' . "\n";
    }
}
?>
<div id="media_albums_intro_search" class="media_albums_intro_search">
<input type="text" id="artist_search" class="artist_search">
</div>
</div>
<div id="media_albums" class="media_albums"></div>
<?php
$PREFIX_TAB_ARTIST = 1;
$PREFIX_TAB_YEAR = 2;

function getHtmlMediaAlbum($datensatz, $prefix_tab)
{
    global $PREFIX_TAB_ARTIST;
    global $PREFIX_TAB_YEAR;
   
    $jahr = "";
    $originalyear = $datensatz["originalyear"];

    if ($datensatz["originalyear"] == 0) {
        $originalyear = $datensatz["year"];
    }

    if ($datensatz["year"] != $datensatz["originalyear"]) {
        $jahr = " ("  . $datensatz["year"] . ")";
    }

    $pref_text = $originalyear;
    $pref_id = "artist_";

    if ($prefix_tab == $PREFIX_TAB_YEAR) {
        $pref_text = $datensatz["albumartist"];
        $pref_id = "year_";
     } 

    return "<div id=\"" . $pref_id . "media_album_" . $datensatz["musicbrainz_albumid"] . "\" class=\"media_album\">"
        . "<p><a href=\"javascript:album_playAlbum('" . $datensatz["musicbrainz_albumid"] . "', 1);\"><span class=\"ui-icon ui-icon-play\"></span></a> | "
        . "<a href=\"javascript:album_playAlbum('" . $datensatz["musicbrainz_albumid"] . "', 2);\"><span class=\"ui-icon ui-icon-arrowthick-1-e\"></span></a> | "
        . "<a href=\"javascript:control_addToRunningTracks(1, '" . $datensatz["musicbrainz_albumid"] . "');\"><span class=\"ui-icon ui-icon-plusthick\"></span></a> | "
        . "<a href=\"javascript:control_flagAlbum('" . $datensatz["musicbrainz_albumid"] . "');\"><span id=\"" . $pref_id . "flag_" . $datensatz["musicbrainz_albumid"] . "\" class=\"ui-icon ui-icon-flag\"></span></a> | "
        . "<a href=\"javascript:control_starAlbum('" . $datensatz["musicbrainz_albumid"] . "', " . $originalyear . ");\"><span id=\"" . $pref_id . "star_" . $datensatz["musicbrainz_albumid"] . "\" class=\"ui-icon ui-icon-star\"></span></a>&nbsp;&nbsp;&nbsp;"
        . "<a href=\"javascript:control_showTracks('" . $pref_id . "tracks_" . $datensatz["musicbrainz_albumid"] . "');\">"
        . $pref_text . '&nbsp;&nbsp;&nbsp;<b>' . $datensatz["album"] . "</b>" . $jahr . "</a></p></div>"
        . "<div class=\"media_album_tracks\" id=\"" . $pref_id . "tracks_" . $datensatz["musicbrainz_albumid"] . "\"></div>\n";
}

$sql = 'SELECT DISTINCT t.album, t.albumartist, t.albumartistsort, t.musicbrainz_albumartistid, UPPER(LEFT(t.albumartistsort, 1)) AS firstletter, t.musicbrainz_albumid, t.year, t.originalyear '
    . 'FROM ' . $dbt_ofa_tracks . ' t ORDER BY t.albumartistsort, t.albumartist, t.originalyear, t.album, t.year, t.musicbrainz_albumid';

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows ($resultat) > 0)
    {
        $artist = '';
        $firstletter = '';

        while ($datensatz = mysqli_fetch_assoc ($resultat)) {
            if ($artist != $datensatz["albumartist"]) {
                if ($artist != '') {
                    echo '</div>' . "\n";
                }

                if ($firstletter != $datensatz["firstletter"]) {
                    $firstletter = $datensatz["firstletter"];

                    echo '<p id="' . $firstletter . '" class="media_album_firstletter">' . $firstletter;
                    echo ' <a href="javascript:control_scrollToLetter();"><span class="ui-icon ui-icon-arrowthick-1-n"></span></a>';
                    echo '</p>' . "\n";
                }

                $artist = $datensatz["albumartist"];

                echo '<div id="' . strtolower($artist) . '" class="media_album_artist_albums">' . "\n";
                echo '<p class="media_album_artist"><b>' . $datensatz["albumartist"] . '</b>&nbsp;&nbsp;&nbsp;';
                echo '<a href="javascript:control_showAlbums(\'' . $datensatz["musicbrainz_albumartistid"] . '\', 1);">Show</a> | ';
                echo '<a href="javascript:album_playArtist(\'' . $datensatz["musicbrainz_albumartistid"] . '\', 1);">Play</a> | ';
                echo '<a href="javascript:album_playArtist(\'' . $datensatz["musicbrainz_albumartistid"] . '\', 2);">Play random</a>';
                echo '</p>' . "\n";
                echo '<div class="media_album_albums" id="albums_' . $datensatz["musicbrainz_albumartistid"] . '"></div>' . "\n";
            }

            echo getHtmlMediaAlbum($datensatz, $PREFIX_TAB_ARTIST);
        }
    }

    mysqli_free_result($resultat);
}
?>
</div>
</div>
<div id="alben_sort_year">
<div id="alben_sort_year_intro" class="alben_sort_year_intro">
<?php
$sql = 'SELECT DISTINCT t.originalyear FROM ' . $dbt_ofa_tracks . ' t ORDER BY t.originalyear DESC';

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows ($resultat) > 0)
    {
        $originalyears = "| ";

        while ($datensatz = mysqli_fetch_assoc ($resultat)) {
            $originalyears .= "<a href=\"javascript:control_scrollToYear('" . $datensatz["originalyear"] . "')\">" . $datensatz["originalyear"] . "</a> | ";
        }

        echo '<div id="alben_sort_year_intro_originalyears" class="alben_sort_year_intro_originalyears">' . $originalyears . '</div>' . "\n";
    }
}
?>
</div>

<?php
$sql = 'SELECT DISTINCT t.album, t.albumartist, t.albumartistsort, t.musicbrainz_albumartistid, t.musicbrainz_albumid, t.year, t.originalyear '
    . 'FROM ' . $dbt_ofa_tracks . ' t ORDER BY t.originalyear DESC, t.albumartistsort, t.albumartist, t.album, t.year, t.musicbrainz_albumid';

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows ($resultat) > 0)
    {
        $artist = '';
        $originalyear = '';

        while ($datensatz = mysqli_fetch_assoc ($resultat)) {
            if ($originalyear != $datensatz["originalyear"]) {
                $originalyear = $datensatz["originalyear"];

                echo '<p id="' . $originalyear . '" class="media_album_originalyear">' . $originalyear;
                echo ' <a href="javascript:control_scrollToYear();"><span class="ui-icon ui-icon-arrowthick-1-n"></span></a>';
                echo '</p>' . "\n";
            }

            $jahr = "";
            $originalyear = $datensatz["originalyear"];

            if ($datensatz["originalyear"] == 0) {
                $originalyear = $datensatz["year"];
            }

            if ($datensatz["year"] != $datensatz["originalyear"]) {
                $jahr = " ("  . $datensatz["year"] . ")";
            }

            echo getHtmlMediaAlbum($datensatz, $PREFIX_TAB_YEAR);
        }
    }

    mysqli_free_result($resultat);
}

mysqli_close($db_link);
?>
</div>
</div>
