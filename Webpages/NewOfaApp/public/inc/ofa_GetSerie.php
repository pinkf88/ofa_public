<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$serieid = 0 + $_GET["serieid"];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$serie = "";
$serie_link = "";

$sql = "SELECT s.serie, w.nummer AS webnummer, w.web, w.pfad AS webpfad, ws.titel, ws.pfad AS seriepfad "
    . "FROM ofa_serie s LEFT JOIN ofa_web_serie ws ON (ws.serieid=$serieid) LEFT JOIN ofa_web w ON (ws.webid=w.id) "
    . "WHERE s.id=$serieid";
// echo $sql;

if ($resultat = mysqli_query($db_link, $sql)) {
    if (mysqli_num_rows($resultat) > 0) {
        $datensatz = mysqli_fetch_assoc($resultat);

        $serie = $datensatz["serie"];

        $url = 'https://www.juergen-reichmann.de';
        $url_end = '';
        $web = $datensatz["web"] . ' | ';

        if ($datensatz["webnummer"] < 0) {
            $url = 'https://www.19xx.de';
        } else if (strpos($datensatz["web"], 'Highlights |') !== false) {
            $url = 'https://www.erde-in-bildern.de';
            $web = '';
        } else {
            $url_end = $datensatz["bildnummer"] . '/';
        }

        $url .= str_replace('/highlights', '', $datensatz["webpfad"]) . $datensatz["seriepfad"] . '/' . $url_end;

        $serie_link .= '<p><b>'
            . '<a href=\"' . $url . '\" target=\"_blank\">' . $web . $datensatz["titel"] . '</a>'
            . '</b>';
    }

    mysqli_free_result($resultat);
}

$serie_bilder = "";
$serie_anzahl = 0;

$sql = "SELECT sb.nr, sb.bildid, sb.dauer, b.nummer, YEAR(b.datum) AS jahr, b.ticket, b.beschreibung, o.ort "
    . "FROM $dbt_ofa_serie_bild sb, $dbt_ofa_bild b, $dbt_ofa_ort o WHERE sb.serieid=$serieid AND sb.bildid=b.id AND b.ortid=o.id ORDER BY sb.nr";
// echo $sql;

if ($resultat = mysqli_query($db_link, $sql)) {
    if (mysqli_num_rows($resultat) > 0) {
        while ($datensatz = mysqli_fetch_assoc($resultat)) {
            $serie_anzahl ++;

            $bildinfo = ofa_GetBildPfad($datensatz["nummer"], $datensatz["ticket"], $datensatz["jahr"]);

            $bilddaten = "";

            if (strlen($bildinfo["pfad"]) > 0) {
                $bilddaten = '<a class=\"fancybox\" rel=\"group\" href=\"' . $bildinfo["pfad"] . '.jpg\" title=\"' . $datensatz["nummer"] . '|'  . $datensatz["bildid"] . '|' . $serieid . '\">'
                    . '<img class=\"mini\" src=\"' . $bildinfo["pfad"] . '.' . $bildinfo["extension"] . '\"></a><br>';
            } else if (strpos ($datensatz["beschreibung"], 'YOUTUBE') !== false) {
                $bilddaten = $datensatz["ort"] . '<br>';
            }

            $serie_bilder .= '<li id=\"id' . $datensatz["bildid"] . '\" class=\"ui-state-default\">'
                . '<div class=\"bildergrid\"><div>' . $datensatz["nr"] . ' | ' . $datensatz["nummer"] . ' | '
                . '<span id=\"dur' . $datensatz["bildid"] . '\">' . $datensatz["dauer"] . ' Sekunden</span></div>'
                . '<div>' . $bilddaten . '</div>'
                . '<div class=\"bildergrid_wertung\">'
                . '<a style=\"color: #2a8af2;\" href=\"javascript:serie_editBild(' . $serieid . ',' . $datensatz["bildid"] . ')\">Edit</a>&nbsp;&nbsp;'
                . '<a style=\"color: #2a8af2;\" href=\"javascript:serie_deleteBild(' . $serieid . ',' . $datensatz["bildid"] . ')\">Del</a>&nbsp;&nbsp;|&nbsp;&nbsp;'
                . '<a style=\"color: #2a8af2;\" href=\"javascript:serie_setDauer(' . $serieid . ',' . $datensatz["bildid"] . ',2)\">2</a> '
                . '<a style=\"color: #2a8af2;\" href=\"javascript:serie_setDauer(' . $serieid . ',' . $datensatz["bildid"] . ',5)\">5</a> '
                . '<a style=\"color: #2a8af2;\" href=\"javascript:serie_setDauer(' . $serieid . ',' . $datensatz["bildid"] . ',8)\">8</a> '
                . '<a style=\"color: #2a8af2;\" href=\"javascript:serie_setDauer(' . $serieid . ',' . $datensatz["bildid"] . ',15)\">15</a> '
                . '<a style=\"color: #2a8af2;\" href=\"javascript:serie_setDauer(' . $serieid . ',' . $datensatz["bildid"] . ',30)\">30</a>'
                . '</div></div>'
                . '</li>';
        }
    }

    mysqli_free_result($resultat);
}

echo '{';
echo '  "serie": "' . $serie . '",';
echo '  "serie_link": "' . $serie_link . '",';
echo '  "serie_anzahl": "' . $serie_anzahl . '",';
echo '  "serie_bilder": "' . $serie_bilder . '"';
echo '}';

mysqli_close($db_link);
?>
