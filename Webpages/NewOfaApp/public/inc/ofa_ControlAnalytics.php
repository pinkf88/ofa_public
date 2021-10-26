<?php
// SELECT w.web, w.pfad, SUM(pageviews) AS pv FROM ofa_web w, ofa_analytics a WHERE domain="www.juergen-reichmann.de" AND w.pfad<>"" AND a.url LIKE CONCAT(w.pfad, "%") AND datum > (DATE(NOW()) - INTERVAL 7 DAY) GROUP BY w.web ORDER BY pv DESC, w.web
// SELECT ws.titel, CONCAT(w.pfad, ws.pfad), SUM(pageviews) AS pv FROM ofa_web w, ofa_web_serie ws, ofa_analytics a WHERE domain="www.juergen-reichmann.de" AND w.web="Deutschland" AND w.pfad<>"" AND ws.webid=w.id AND a.url LIKE CONCAT(w.pfad, ws.pfad, "%") AND datum > (DATE(NOW()) - INTERVAL 7 DAY) GROUP BY CONCAT(w.pfad, ws.pfad) ORDER BY ws.titel
// SELECT a.url, l.land, o.ort, m.motiv, a.title, SUM(pageviews) AS pv FROM ofa_analytics a, ofa_motiv m, ofa_ort o, ofa_land l WHERE domain="www.juergen-reichmann.de" AND a.url LIKE "/auswahl.php?motiv%" AND m.id=SUBSTRING(SUBSTRING_INDEX(url, "&", 1), 20) AND o.id=m.ortid AND l.id=o.landid AND datum > (DATE(NOW()) - INTERVAL 30 DAY) GROUP BY url ORDER BY l.land, o.ort, m.motiv, pv DESC
// SELECT a.url, o.ort, m.motiv, a.title, SUM(pageviews) AS pv FROM ofa_analytics a, ofa_motiv m, ofa_ort o WHERE domain="www.juergen-reichmann.de" AND a.url LIKE "/auswahl.php?motiv%" AND m.id=SUBSTRING(SUBSTRING_INDEX(url, "&", 1), 20) AND o.id=m.ortid AND datum > (DATE(NOW()) - INTERVAL 7 DAY) GROUP BY url ORDER BY o.ort, m.motiv, pv DESC
// SELECT a.url, o.ort, m.motiv, SUM(pageviews) AS pv FROM ofa_analytics a, ofa_motiv m, ofa_ort o WHERE domain="www.juergen-reichmann.de" AND a.url LIKE "/auswahl.php?motiv%" AND m.id=SUBSTRING(SUBSTRING_INDEX(url, "&", 1), 20) AND o.id=m.ortid AND o.landid=1 AND datum > (DATE(NOW()) - INTERVAL 30 DAY) GROUP BY url ORDER BY o.ort, m.motiv, pv DESC

include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

// print_r($_GET);

$type = $_GET["type"];

$count_days = 30;

if (isset($_GET["days"])) {
    $count_days = 0 + $_GET["days"];
}

$web = '';

if (isset($_GET["web"])) {
    $web = $_GET["web"];
}

$landid = 0;

if (isset($_GET["landid"])) {
    $landid = 0 + $_GET["landid"];
}

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

if ($type == 'overview') {
    $sql = 'SELECT MAX(datum) AS date_max, (DATE(NOW()) - INTERVAL ' . $count_days . ' DAY) AS date_min FROM ofa_analytics';
    $date_min = '';
    $date_max = '';

    if ($resultat = mysqli_query($db_link, $sql)) {
        $datensatz = mysqli_fetch_assoc($resultat);
        $date_min = date_mysql2german($datensatz["date_min"]);
        $date_max = date_mysql2german($datensatz["date_max"]);
    }

    $sql = 'SELECT w.web, w.pfad, SUM(pageviews) AS pv FROM ofa_web w, ofa_analytics a '
        . 'WHERE domain="www.juergen-reichmann.de" AND w.pfad<>"" AND a.url LIKE CONCAT(w.pfad, "%") AND a.datum >= (DATE(NOW()) - INTERVAL ' . $count_days . ' DAY) '
        . 'GROUP BY w.web ORDER BY pv DESC, w.web';

    $webs = array();
    $paths = array();
    $pageviews = array();

    if ($resultat = mysqli_query($db_link, $sql)) {
        if (mysqli_num_rows($resultat) > 0) {
            while ($datensatz = mysqli_fetch_assoc($resultat)) {
                $webs[] = $datensatz["web"];
                $paths[] = $datensatz["pfad"];
                $pageviews[] = $datensatz["pv"];
            }
        }
    }

    echo '{';
    echo '  "date_min": "' . $date_min . '",';
    echo '  "date_max": "' . $date_max . '",';
    echo '  "webs": ' . json_encode($webs) . ',';
    echo '  "paths": ' . json_encode($paths) . ',';
    echo '  "pageviews":' . json_encode($pageviews);
    echo '}';
} else if ($type == 'web') {
    $sql = 'SELECT ws.titel, CONCAT(w.pfad, ws.pfad, "/") AS webpfad, SUM(pageviews) AS pv '
        . 'FROM ofa_web w, ofa_web_serie ws, ofa_analytics a '
        . 'WHERE domain="www.juergen-reichmann.de" AND w.web="' . $web . '" AND w.pfad<>"" AND ws.webid=w.id '
        . 'AND a.url LIKE CONCAT(w.pfad, ws.pfad, "/%") AND a.datum >= (DATE(NOW()) - INTERVAL ' . $count_days . ' DAY) '
        . 'GROUP BY CONCAT(w.pfad, ws.pfad) ORDER BY ws.nr';

    $titles = array();
    $paths = array();
    $pageviews = array();

    if ($resultat = mysqli_query($db_link, $sql)) {
        if (mysqli_num_rows($resultat) > 0) {
            while ($datensatz = mysqli_fetch_assoc($resultat)) {
                $titles[] = $datensatz["titel"];
                $paths[] = $datensatz["webpfad"];
                $pageviews[] = $datensatz["pv"];
            }
        }
    }

    $sql = 'SELECT ws.titel, CONCAT(w.pfad, ws.pfad, "/") AS webpfad, SUM(pageviews) AS pv '
        . 'FROM ofa_web w, ofa_web_serie ws, ofa_analytics a '
        . 'WHERE domain="www.juergen-reichmann.de" AND w.web="' . $web . '" AND w.pfad<>"" AND ws.webid=w.id '
        . 'AND a.url LIKE CONCAT(w.pfad, ws.pfad, "/") AND a.datum >= (DATE(NOW()) - INTERVAL ' . $count_days . ' DAY) '
        . 'GROUP BY CONCAT(w.pfad, ws.pfad) ORDER BY ws.nr';

    if ($resultat = mysqli_query($db_link, $sql)) {
        if (mysqli_num_rows($resultat) > 0) {
            while ($datensatz = mysqli_fetch_assoc($resultat)) {
                for ($i = 0; $i < count($paths); $i++) {
                    if ($datensatz["webpfad"] == $paths[$i]) {
                        $pageviews[$i] = $datensatz["pv"] . ' | ' . (intval($pageviews[$i]) - intval($datensatz["pv"]));
                        break;
                    }
                }
            }
        }
    }

    for ($i = 0; $i < count($pageviews); $i++) {
        if (strpos($pageviews[$i], ' | ') === false) {
            $pageviews[$i] = '0 | ' . $pageviews[$i];
        }
    }

    echo '{';
    echo '  "titles": ' . json_encode($titles) . ',';
    echo '  "paths": ' . json_encode($paths) . ',';
    echo '  "pageviews":' . json_encode($pageviews);
    echo '}';
} else if ($type == 'auswahl') {
    $sql = 'SELECT l.id, l.land, SUM(pageviews) AS pv FROM ofa_analytics a, ofa_motiv m, ofa_ort o, ofa_land l '
        . 'WHERE domain="www.juergen-reichmann.de" AND a.url LIKE "/auswahl.php?motiv%" AND m.id=SUBSTRING(SUBSTRING_INDEX(url, "&", 1), 20) '
        . 'AND o.id=m.ortid AND l.id=o.landid AND a.datum >= (DATE(NOW()) - INTERVAL ' . $count_days . ' DAY) GROUP BY l.land ORDER BY l.land, pv DESC';

    // echo $sql;

    $landids = array();
    $laender = array();
    $pageviews = array();

    if ($resultat = mysqli_query($db_link, $sql)) {
        if (mysqli_num_rows($resultat) > 0) {
            while ($datensatz = mysqli_fetch_assoc($resultat)) {
                $landids[] = $datensatz["id"];
                $laender[] = $datensatz["land"];
                $pageviews[] = $datensatz["pv"];
            }
        }
    }

    echo '{';
    echo '  "landids": ' . json_encode($landids) . ',';
    echo '  "laender": ' . json_encode($laender) . ',';
    echo '  "pageviews":' . json_encode($pageviews);
    echo '}';
} else if ($type == 'land') {
    $sql = '(SELECT a.url, o.ort, m.motiv, SUM(pageviews) AS pv FROM ofa_analytics a, ofa_motiv m, ofa_ort o '
        . 'WHERE domain="www.juergen-reichmann.de" AND a.url LIKE "/auswahl.php?motiv%" AND m.id=SUBSTRING(SUBSTRING_INDEX(url, "&", 1), 20) '
        . 'AND o.id=m.ortid AND o.landid=' . $landid . ' AND a.datum >= (DATE(NOW()) - INTERVAL ' . $count_days . ' DAY) GROUP BY url) '
        . 'UNION '
        . '(SELECT a.url, o.ort, "" AS motiv, SUM(pageviews) AS pv FROM ofa_analytics a, ofa_ort o '
        . 'WHERE domain="www.juergen-reichmann.de" AND a.url LIKE "/auswahl.php?ort%" AND o.id=SUBSTRING(SUBSTRING_INDEX(url, "&", 1), 18) '
        . 'AND o.landid=' . $landid . ' AND a.datum >= (DATE(NOW()) - INTERVAL ' . $count_days . ' DAY) GROUP BY url) '
        . 'ORDER BY ort, motiv, pv DESC';
    
    $ortemotive = array();
    $urls = array();
    $pageviews = array();

    if ($resultat = mysqli_query($db_link, $sql)) {
        if (mysqli_num_rows($resultat) > 0) {
            while ($datensatz = mysqli_fetch_assoc($resultat)) {
                $ortmotiv = $datensatz["ort"];

                if ($datensatz["motiv"] != '') {
                    $ortmotiv .= ' - ' . $datensatz["motiv"];
                }

                $ortemotive[] = $ortmotiv;
                $urls[] = $datensatz["url"];
                $pageviews[] = $datensatz["pv"];
            }
        }
    }

    echo '{';
    echo '  "ortemotive": ' . json_encode($ortemotive) . ',';
    echo '  "urls": ' . json_encode($urls) . ',';
    echo '  "pageviews":' . json_encode($pageviews);
    echo '}';
} else {
    echo '{}';
}

mysqli_close($db_link);
?>
