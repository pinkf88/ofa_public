<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$bildid = 0 + $_GET["id"];

$version = 1;

if (isset($_GET["version"])) {
    $version = $_GET["version"];
}

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$nummer = "0";
$datei = "0";
$beschreibung = "";
$ticket = 0;
$jahr = 0;
$info = "";
$aufnahmedatum = "";
$techdaten = "";
$polygon = "";
$geodaten = "";

$sql = "SELECT nummer, datei, beschreibung, info, ticket, YEAR(datum) as jahr FROM $dbt_ofa_bild WHERE id=$bildid";

if ($resultat = mysqli_query($db_link, $sql)) {
    if (mysqli_num_rows ($resultat) > 0) {
        $datensatz = mysqli_fetch_assoc ($resultat);
        $nummer = $datensatz["nummer"];
        $datei = $datensatz["datei"];
        $beschreibung = $datensatz["beschreibung"];
        $ticket = 0 + $datensatz["ticket"];
        $jahr = 0 + $datensatz["jahr"];
        $info = $datensatz["info"];
    }

    mysqli_free_result($resultat);
}

$motivliste = "";
$serieliste = "";
$zusatzinfo = "";
$bilddaten = "";
$polygon = "";
$bilddata = "";

if ($version == 1) {
    $motivliste = '<p class=\"motivliste\" id=\"motivliste\"> ';

    $sql = "SELECT m.id, m.motiv, bm.default FROM $dbt_ofa_motiv m, $dbt_ofa_bild_motiv bm WHERE bm.bildid=$bildid AND bm.motivid=m.id ORDER BY m.motiv";

    if ($resultat = mysqli_query($db_link, $sql)) {
        if (mysqli_num_rows ($resultat) > 0) {
            $motivliste .= '| ';

            while ($datensatz = mysqli_fetch_assoc ($resultat)) {
                if (0 + $datensatz["default"] == 1)
                    $motivliste .= '<b>' . $datensatz["motiv"] . '</b> | ';
                else
                    $motivliste .= '<a class=\"motiv\" href=\"javascript:bild_setDefaultMotiv(' . $bildid . ',' . $datensatz["id"] . ')\">' . $datensatz["motiv"] . '</a> | ';
            }
        }

        mysqli_free_result($resultat);
    }

    $motivliste .= '</p>';

    $serieliste = '<p class=\"serieliste\" id=\"serieliste\">';

    $sql = "SELECT DISTINCT b.nummer AS bildnummer, w.nummer AS webnummer, w.web, w.pfad AS webpfad, ws.titel, ws.pfad AS seriepfad "
        . "FROM $dbt_ofa_serie_bild sb, $dbt_ofa_web_serie ws, $dbt_ofa_web w, $dbt_ofa_bild b "
        . "WHERE b.id=$bildid AND sb.bildid=$bildid AND sb.serieid=ws.serieid AND ws.webid=w.id ORDER BY ws.titel";

    if ($resultat = mysqli_query($db_link, $sql)) {
        if (mysqli_num_rows ($resultat) > 0) {
            while ($datensatz = mysqli_fetch_assoc ($resultat)) {
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

                $serieliste .= '<b><i>'
                    . '<a href=\"' . $url . '\" target=\"_blank\">' . $web . $datensatz["titel"] . '</a>'
                    . '</i></b><br>';
            }
        }

        mysqli_free_result($resultat);
    }

    $serieliste .= '</p>';

    if ($info != "") {
        $zusatzinfo = '<p><i>Info: ' . $info . '</i></p>';
    }
}

$bildinfo = array();

if (strpos($beschreibung, 'YOUTUBE=') === false) {
    $bildinfo = ofa_getBildPfad(0 + $nummer, $ticket, $jahr);
}
// print_r($bildinfo);

if ($version == 1) {
    $videoinfo = '""';
    $youtubeid = '';

    if (strpos($beschreibung, 'YOUTUBE=') === false) {
        if (strlen($bildinfo["pfad"]) > 0) {
            $bilddaten = '<a class=\"fancybox\" rel=\"group\" href=\"' . $bildinfo["pfad"] . '.jpg\">'
                . '<img class=\"mini\" src=\"' . $bildinfo["pfad"] . '.' . $bildinfo["extension"] . '\"></a><br>';
        }
    }

    $nummer = '<p><b>' . $nummer . '</b>';

    if (strpos($beschreibung, 'YOUTUBE=') === false) {
        $sql = "SELECT bd.BildNr, bd.Aufnahmedatum, bd.Kameradatum, bd.Zeit, bd.Blende, bd.ISO, bd.Brennweite, "
            . "bd.Laenge, bd.Breite, bd.Hoehe, bd.polygon, bd.locations FROM $dbt_ofa_bilddaten bd WHERE bd.BildNr=$datei";

        if ($resultat = mysqli_query($db_link, $sql)) {
            if (mysqli_num_rows ($resultat) > 0) {
                $datensatz = mysqli_fetch_assoc ($resultat);

                if ($datensatz["Aufnahmedatum"] <> "") {
                    if ($datensatz["Aufnahmedatum"] == '0000-00-00 00:00:00') {
                        $aufnahmedatum = '<p class=\"aufnahmedatum\">' . $datensatz["Kameradatum"] . '</p>';
                    } else {
                        $aufnahmedatum = '<p class=\"aufnahmedatum\">' . $datensatz["Aufnahmedatum"] . '</p>';
                    }

                    $techdaten = '<p class=\"techdaten\">' . $datensatz["Zeit"] . ' / ';
                    
                    if ($datensatz["Blende"] == "") {
                        $techdaten .= '-';
                    } else {
                        $techdaten .= $datensatz["Blende"];
                    }

                    $techdaten .= ' / ISO ' . $datensatz["ISO"] . ' / ' . $datensatz["Brennweite"];
                    
                    if ($datensatz["Hoehe"] != 0 && $datensatz["Hoehe"] != '' && $datensatz["Hoehe"] != '0') {
                        $techdaten .= ' / ' . $datensatz["Hoehe"] . ' m';
                    }
                    
                    $techdaten .= '</p>';
                }

                if ($datensatz["Laenge"] <> 0 && $datensatz["Breite"] <> 0) {
                    $breite = (float)$datensatz["Breite"] / 10000.0;
                    $laenge = (float)$datensatz["Laenge"] / 10000.0;

                    $geodaten = '<p class=\"geodaten\">';
                    $geodaten .= '<a class=\"geodel\" href=\"https://www.openstreetmap.org/query?lat=' . $breite . '&lon=' . $laenge . '\">' . $breite . '° / ' . $laenge . '°</a>&nbsp;&nbsp;';
                    $geodaten .= '<a class=\"geodel\" href=\"https://www.peakfinder.org/de/?lat=' . $breite . '&lng=' . $laenge . '\">Peakfinder</a>&nbsp;&nbsp;';
                    $geodaten .= '<a class=\"geodel\" href=\"javascript:bild_deleteGeodaten(' . $bildid . ', ' . $datei . ')\">Löschen</a>&nbsp;&nbsp;';
                    $geodaten .= '<a class=\"geocopy\" href=\"javascript:bild_copyGeodaten(' . $datensatz["Breite"] . ', ' . $datensatz["Laenge"] . ', ' . $datensatz["Hoehe"] . ')\">Kopieren</a>';
                    $geodaten .= '</p>';
                } else {
                    $geodaten .= '<p class=\"geodaten\"><a class=\"geoupdate\" href=\"javascript:bild_updateGeodaten(' . $bildid . ', ' . $datei . ')\">Update Geodaten</a></p>';
                }

                if ($datensatz["polygon"] != null || $datensatz["polygon"] <> "") {
                    $polygon = 'Polygon';

                    if ($datensatz["locations"] != null || $datensatz["locations"] <> "") {
                        $polygon .= ' | Locations';
                    }
                }
            }

            mysqli_free_result($resultat);
        }
    } else {
        $video_arr = explode(' | ', $beschreibung);
        $youtubeid = str_replace('YOUTUBE=', '', $video_arr[0]);
        // print_r($video_arr);

        if (array_key_exists(1, $video_arr)) {
            $file = '../data/videolist_jr.json';
            $json = file_get_contents($file);
            $videolist_jr = json_decode($json, true);

            for ($i = 0; $i < count($videolist_jr); $i++) {
                if ($videolist_jr[$i]["title"] == $video_arr[1]) {
                    // print_r($videolist_jr[$i]);
                    $videoinfo = json_encode($videolist_jr[$i]);
                    break;
                }
            }
        }
    }

    $nummer .= '</p>';

    echo '{';
    echo '  "nummer": "' . $nummer . '",';
    echo '  "motivliste": "' . $motivliste . '",';
    echo '  "serieliste": "' . $serieliste . '",';
    echo '  "zusatzinfo": "' . $zusatzinfo . '",';
    echo '  "aufnahmedatum": "' . $aufnahmedatum . '",';
    echo '  "techdaten": "' . $techdaten . '",';
    echo '  "geodaten": "' . $geodaten . '",';
    echo '  "bilddaten": "' . $bilddaten . '",';
    echo '  "polygon": "' . $polygon .  '",';
    echo '  "youtubeid": "' . $youtubeid .  '",';
    echo '  "videoinfo": ' . $videoinfo .  '';
    echo '}';
} else if ($version == 2) {
    $bildpath = $bildinfo["pfad"] . '.' . $bildinfo["extension"];
    $bilddata = '{ "bildid": "' . $bildid . '", "nummer": "' . $nummer . '", "bildpath": "' . $bildpath . '" }';

    echo '{';
    echo '  "bilddata": ' . $bilddata;
    echo '}';
} else {
    echo '{}';
}

mysqli_close($db_link);
?>
