<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$bildid = 0 + $_GET["id"];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$nummer = "0";
$datei = "0";
$beschreibung = "";
$ticket = 0;
$jahr = 0;
$info = "";
$polygon = "";
$geodaten = "";

$sql = "SELECT nummer, datei, beschreibung, ticket, YEAR(datum) as jahr FROM $dbt_ofa_bild WHERE id=$bildid";

if ($resultat = mysqli_query($db_link, $sql)) {
    if (mysqli_num_rows ($resultat) > 0) {
        $datensatz = mysqli_fetch_assoc ($resultat);
        $nummer = $datensatz["nummer"];
        $datei = $datensatz["datei"];
        $beschreibung = $datensatz["beschreibung"];
        $ticket = 0 + $datensatz["ticket"];
        $jahr = 0 + $datensatz["jahr"];
    }

    mysqli_free_result($resultat);
}

$serieliste = "";
$zusatzinfo = "";
$bilddaten  = "";
$breite     = 0.0;
$laenge     = 0.0;
$polygon    = "[]";
$locations  = "";
$dists      = "";
$bilddata   = "";

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

$bildinfo = ofa_getBildPfad(0 + $nummer, $ticket, $jahr);

// print_r($bildinfo);

if (strlen($bildinfo["pfad"]) > 0) {
    $bilddaten = '<div><img class=\"bildinformation_pic\" src=\"' . $bildinfo["pfad"] . '.jpg\"></div>';
}

$sql = "SELECT bd.BildNr, bd.Laenge, bd.Breite, bd.Hoehe, bd.polygon, bd.locations, bd.dists FROM $dbt_ofa_bilddaten bd WHERE bd.BildNr=$datei";

if ($resultat = mysqli_query($db_link, $sql)) {
    if (mysqli_num_rows ($resultat) > 0) {
        $datensatz = mysqli_fetch_assoc ($resultat);

        if ($datensatz["Laenge"] <> 0 && $datensatz["Breite"] <> 0) {
            $breite = (float)$datensatz["Breite"] / 10000.0;
            $laenge = (float)$datensatz["Laenge"] / 10000.0;

            $geodaten = '<p class=\"geodaten\">';
            $geodaten .= '<a class=\"geodel\" href=\"https://www.openstreetmap.org/query?lat=' . $breite . '&lon=' . $laenge . '\">Open Street Map</a>&nbsp;&nbsp;&nbsp;&nbsp;';
            $geodaten .= '<a class=\"geodel\" href=\"https://www.peakfinder.org/de/?lat=' . $breite . '&lng=' . $laenge . '\">Peakfinder</a>';
            $geodaten .= '</p>';
        }

        if ($datensatz["polygon"] != null || $datensatz["polygon"] <> "") {
            $polygon = $datensatz["polygon"];
        }

        if ($datensatz["locations"] != null || $datensatz["locations"] <> "") {
            $locations = $datensatz["locations"];
        }

        if ($datensatz["dists"] != null || $datensatz["dists"] <> "") {
            $dists = $datensatz["dists"];
        }
    }

    mysqli_free_result($resultat);
}

echo '{';
echo '  "bildid": "' . $bildid . '",';
echo '  "nummer": "' . $nummer . '",';
echo '  "bildpath": "' . $bildinfo["pfad"] . '.jpg",';
echo '  "breite": "' . $breite . '",';
echo '  "laenge": "' . $laenge . '",';
echo '  "serieliste": "' . $serieliste . '",';
echo '  "geodaten": "' . $geodaten . '",';
echo '  "bilddaten": "' . $bilddaten . '",';
echo '  "polygon": "' . $polygon .  '",';
echo '  "locations": "' . $locations .  '",';
echo '  "dists": "' . $dists .  '"';
echo '}';

mysqli_close($db_link);
?>
