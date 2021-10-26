<?php
$playlist_categories = array(
    array("STUDIO_", "Studio"),
    array("LIVE_", "Live"),
    array("JR_IN_CONCERT_", "JR In Concert"),
    array("SONSTIGE_", "Sonstige")
);
?>
<div id="control_tab_playlists">
<h4 class="control">PLAYLISTEN</h4>
<h5 class="control" id="playall">Play All</h5>
<hr class="control">
<div class="control_person">
    <select name="control_person" id="control_person" class="mittel">
        <option value="0" selected="selected">Alle Personen</option>
        <option value="1">Jürgen</option>
        <option value="2">Elke</option>
    </select>
</div>
<div class="control_artist">
    <select name="control_artist" id="control_artist" class="mittel">
        <option value="0" selected="selected">Alle Künstler</option>
<?php
$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "SELECT DISTINCT t.musicbrainz_albumartistid, t.albumartist FROM $dbt_ofa_tracks t ORDER BY t.albumartist";
// echo $sql;

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows ($resultat) > 0) {
        while ($datensatz = mysqli_fetch_assoc ($resultat)) {
            echo "        <option value=\"" . $datensatz["musicbrainz_albumartistid"] . "\">" . $datensatz["albumartist"] . "</option>\n";
        }
    }

    mysqli_free_result($resultat);
}
?>
    </select>
</div>
<div class="control_music">
    <select name="control_music" id="control_music" class="mittel">
        <option value="0" selected="selected">Alle</option>
        <option value="2">Studio</option>
        <option value="3">Live</option>
    </select>
</div>
<?php
$years = "";
$sql = "SELECT MIN(t.originalyear) AS year_min, MAX(t.originalyear) AS year_max FROM $dbt_ofa_tracks t WHERE t.originalyear>1900";

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows ($resultat) > 0)
    {
        $datensatz = mysqli_fetch_assoc ($resultat);

        for ($year = intval($datensatz["year_max"]); $year >= intval($datensatz["year_min"]); $year--) {
            $years .= "        <option value=\"" . $year . "\">" . $year . "</option>\n";
        }
    }

    mysqli_free_result($resultat);
}
?>
<div class="control_year_from">
    <select name="control_year_from" id="control_year_from" class="mittel">
        <option value="0" selected="selected">Alle Jahre (von)</option>
<?php echo $years; ?>
    </select>
</div>
<div class="control_year_to">
    <select name="control_year_to" id="control_year_to" class="mittel">
        <option value="0" selected="selected">Alle Jahre (bis)</option>
<?php echo $years; ?>
    </select>
</div>
<div class="control_no_compilations">
    <input type="checkbox" id="no_compilations" name="no_compilations"><label class="no_compilations" for="no_compilations">Keine Compilations</label>
</div>
<p class="control_playbuttons"><a href="javascript:control_playMusic(1);">Play</a> | <a href="javascript:control_playMusic(2);">Play random</a></p>
<p><span id="lastinfo"></span></p>
<h5 class="control" id="playlists">Playlists</h5>
<hr class="control">
<?php
for ($i = 0; $i < count($playlist_categories); $i++) {
    echo '<p class="media_playlist_caption">' . $playlist_categories[$i][1] . '</p>' . "\n";
    echo '<div id="PLAYLIST_' . $playlist_categories[$i][0] . '" class="media_playlist_list"></div>' . "\n";
}

mysqli_close($db_link);
?>
</div>
