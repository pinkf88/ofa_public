<div id="control_tab_pictures">
<h4 class="control">BILDER</h4>
<h5 class="control" id="playall">Play All</h5>
<hr class="control">
<div class="control_country">
    <select name="control_country" id="control_country" class="laenger">
        <option value="0" selected="selected">Alle Länder</option>
<?php
$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "SELECT DISTINCT l.id, l.land FROM ofa_land l, ofa_ort o, ofa_bild b WHERE b.ortid=o.id AND o.landid=l.id ORDER BY l.land";

if ($resultat = mysqli_query($db_link, $sql)) {
    if (mysqli_num_rows ($resultat) > 0) {
        while ($datensatz = mysqli_fetch_assoc ($resultat)) {
            echo "        <option value=\"" . $datensatz["id"] . "\">" . $datensatz["land"] . "</option>\n";
        }
    }

    mysqli_free_result($resultat);
}
?>
    </select>
</div>
<div class="control_location">
    <select name="control_location" id="control_location" class="laenger">
        <option value="0" selected="selected">Alle Orte</option>
<?php
$sql = "SELECT DISTINCT o.id, o.ort FROM ofa_ort o, ofa_bild b WHERE b.ortid=o.id ORDER BY o.ort";

if ($resultat = mysqli_query($db_link, $sql)) {
    if (mysqli_num_rows ($resultat) > 0)
    {
        while ($datensatz = mysqli_fetch_assoc ($resultat)) {
            echo "        <option value=\"" . $datensatz["id"] . "\">" . $datensatz["ort"] . "</option>\n";
        }
    }

    mysqli_free_result($resultat);
}
?>
    </select>
</div>
<div class="control_year">
    <select name="control_year" id="control_year" class="mittel">
        <option value="0" selected="selected">Alle Jahre</option>
<?php
$sql = "SELECT DISTINCT YEAR(b.datum) AS jahr FROM ofa_ort o, ofa_bild b WHERE b.ortid=o.id ORDER BY jahr DESC";

if ($resultat = mysqli_query($db_link, $sql)) {
    if (mysqli_num_rows ($resultat) > 0) {
        while ($datensatz = mysqli_fetch_assoc ($resultat)) {
            echo "        <option value=\"" . $datensatz["jahr"] . "\">" . $datensatz["jahr"] . "</option>\n";
        }
    }

    mysqli_free_result($resultat);
}
?>
    </select>
</div>
<div class="control_search">
<input type="text" id="control_search" class="laenger">
</div>
<p><a href="javascript:control_playPictures(1);">Play</a> | <a href="javascript:control_playPictures(2);">Play random</a> | <a href="javascript:control_playPictures(3);">Play long</a></p>
<h5 class="control" id="shows">Shows</h5>
<hr class="control">
<div id="media_shows" class="media_shows"></div>
<?php
$sql = "SELECT s.id, s.serie FROM $dbt_ofa_serie s WHERE s.serie LIKE \"%Show | %\" ORDER BY s.serie";

if ($resultat = mysqli_query($db_link, $sql)) {
    if (mysqli_num_rows ($resultat) > 0) {
        while ($datensatz = mysqli_fetch_assoc ($resultat)) {
            $html = "<div class=\"media_show\">"
                . "<p><a href=\"javascript:serie_playSerie('" . $datensatz["id"] . "',1);\"><span class=\"ui-icon ui-icon-play\"></span></a> | "
                . "<a href=\"javascript:serie_playSerie('" . $datensatz["id"] . "',2);\"><span class=\"ui-icon ui-icon-arrowthick-1-e\"></a> | "
                . "<a href=\"javascript:serie_playSerie('" . $datensatz["id"] . "',3);\"><span class=\"ui-icon ui-icon-circle-triangle-e\"></a>"
                . "&nbsp;&nbsp;&nbsp;<a href=\"javascript:serie_playSerie('" . $datensatz["id"] . "',1);\">" . substr($datensatz["serie"], 6) . "</a>"
                . "</p></div>\n";

            echo $html;
        }
    }

    mysqli_free_result($resultat);
}
?>
<h5 class="control" id="highlights">Highlights</h5>
<hr class="control">
<?php
$sql = 'SELECT DISTINCT sb.serieid, s.serie, COUNT(sb.bildid) AS anzahl FROM ofa_serie_bild sb, ofa_serie s '
    . 'WHERE sb.serieid=s.id AND s.serie LIKE "Highlights | %" GROUP BY sb.serieid ORDER BY s.serie';

if ($resultat = mysqli_query($db_link, $sql)) {
    $highlight = '';
    $anzahl = 0;
    $serieids = '';
    $html = '';

    while ($datensatz = mysqli_fetch_assoc ($resultat)) {
        $thema = explode(" | ", $datensatz["serie"]);

        if ($highlight != ($thema[1] . ' | ' . $thema[2])) {
            if ($highlight != '') {
                $serieids = rtrim($serieids, ',');

                echo '<div class="media_serien_highlight">' . "\n";
                echo "<p><a href=\"javascript:serie_playSerie('" . $serieids . "',1);\"><span class=\"ui-icon ui-icon-play\"></span></a> | ";
                echo "<a href=\"javascript:serie_playSerie('" . $serieids . "',2);\"><span class=\"ui-icon ui-icon-arrowthick-1-e\"></a> | ";
                echo "<a href=\"javascript:serie_playSerie('" . $serieids . "',3);\"><span class=\"ui-icon ui-icon-circle-triangle-e\"></a>";
                echo "&nbsp;&nbsp;&nbsp;<a href=\"javascript:serie_playSerie('" . $serieids . "',1);\"><b>" . $highlight . "</b> (" . $anzahl . " Bilder)</a>";
                echo "</p></div>\n";
            }

            $highlight = $thema[1] . ' | ' . $thema[2];
            $anzahl = 0;
            $serieids = '';
        }

        $anzahl += $datensatz["anzahl"];
        $serieids .= $datensatz["serieid"] . ',';
    }

    echo '<div class="media_serien_serie">' . "\n";
    echo "<p><a href=\"javascript:serie_playSerie('" . $serieids . "',1);\"><span class=\"ui-icon ui-icon-play\"></span></a> | ";
    echo "<a href=\"javascript:serie_playSerie('" . $serieids . "',2);\"><span class=\"ui-icon ui-icon-arrowthick-1-e\"></a> | ";
    echo "<a href=\"javascript:serie_playSerie('" . $serieids . "',3);\"><span class=\"ui-icon ui-icon-circle-triangle-e\"></a>";
    echo "&nbsp;&nbsp;&nbsp;<a href=\"javascript:serie_playSerie('" . $serieids . "',1);\"><b>" . $highlight . "</b> (" . $anzahl . " Bilder)</a>";
    echo "</p></div>\n";

    mysqli_free_result($resultat);
}
?>
<h5 class="control" id="serien">Serien</h5>
<hr class="control">
<div id="media_serien_intro_search" class="media_serien_intro_search">
<input type="text" id="serie_search" class="serie_search">
</div>
<div id="media_serien" class="media_serien">
<?php
$sql = 'SELECT DISTINCT sb.serieid, s.serie, COUNT(sb.bildid) AS anzahl FROM ofa_serie_bild sb, ofa_serie s WHERE sb.serieid=s.id GROUP BY sb.serieid ORDER BY s.serie';

if ($resultat = mysqli_query($db_link, $sql)) {
    while ($datensatz = mysqli_fetch_assoc ($resultat)) {
        echo '<div id="' . strtolower($datensatz["serie"]) . '" class="media_serien_serie">' . "\n";
        echo "<p><a href=\"javascript:serie_playSerie('" . $datensatz["serieid"] . "',1);\"><span class=\"ui-icon ui-icon-play\"></span></a> | ";
        echo "<a href=\"javascript:serie_playSerie('" . $datensatz["serieid"] . "',2);\"><span class=\"ui-icon ui-icon-arrowthick-1-e\"></a> | ";
        echo "<a href=\"javascript:serie_playSerie('" . $datensatz["serieid"] . "',3);\"><span class=\"ui-icon ui-icon-circle-triangle-e\"></a>";
        echo "&nbsp;&nbsp;&nbsp;<a href=\"javascript:serie_playSerie('" . $datensatz["serieid"] . "',1);\"><b>" . $datensatz["serie"] . "</b> (" . $datensatz["anzahl"] . " Bilder)</a>";
        echo "</p></div>\n";
    }

    mysqli_free_result($resultat);
}
    
mysqli_close($db_link);
?>
</div>
</div>
