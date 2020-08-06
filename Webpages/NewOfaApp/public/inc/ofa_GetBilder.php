<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$bildtyp = 0 + $_GET["bildtyp"];
$jahr = 0 + $_GET["jahr"];
$ortid = 0 + $_GET["ortid"];
$landid = 0 + $_GET["landid"];
$nummer_von = 0 + $_GET["nummer_von"];
$nummer_bis = 0 + $_GET["nummer_bis"];
$suchtext = $_GET["suchtext"];
$wertung_min = 0 + $_GET["wertung_min"];
$countperpage = 0 + $_GET["countperpage"];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$bilder = "";
$anzahl = 0;

$where_bildtyp = "";

if ($bildtyp == 1) {
    // Bilder
    $where_bildtyp = " AND b.ticket=0";
} else if ($bildtyp == 2) {
    // Tickets
    $where_bildtyp = " AND b.ticket=1";
}

$where_jahr = "";

if ($jahr > 0) {
    $where_jahr = " AND YEAR(b.datum)=" . $jahr;
}

$where_ortid = "";

if ($ortid > 0) {
    $where_ortid = " AND b.ortid=" . $ortid;
}

$where_landid = "";

if ($landid > 0) {
    $where_landid = " AND o.landid=" . $landid;
}

$where_nummern = "";

if ($nummer_von > 0 && $nummer_bis == 0) {
    $where_nummern = " AND b.nummer=" . $nummer_von;
} else if ($nummer_von > 0 && $nummer_bis > 0) {
    $where_nummern = " AND b.nummer>=" . $nummer_von . " AND b.nummer<=" . $nummer_bis;
}

$where_suchtext = "";

if ($suchtext != "") {
    $pos = strpos($suchtext, 'DATUM');

    if ($pos === false) {
        $where_suchtext = " AND (b.beschreibung LIKE '%" . $suchtext . "%' OR b.bemerkung LIKE '%" . $suchtext . "%' OR b.info LIKE '%" . $suchtext . "%')";
    } else {
        $datum = explode('-', substr($suchtext, 5));
        $where_suchtext = " AND DAY(b.datum)='" . $datum[0] . "' AND MONTH(b.datum)='" . $datum[1] . "'";
    }
}

$where_wertung_min = "";

if ($wertung_min > 0) {
    $where_wertung_min = " AND b.wertung>=" . $wertung_min;
}

$limit = "";

if ($countperpage > 0) {
    $limit = " LIMIT " . $countperpage;
}


$sql = "SELECT b.id AS bildid, b.nummer, YEAR(b.datum) AS jahr, b.ticket, b.beschreibung, b.wertung, o.ort "
    . "FROM $dbt_ofa_bild b, $dbt_ofa_ort o WHERE b.ortid=o.id "
    . $where_bildtyp . $where_jahr . $where_ortid . $where_landid . $where_nummern . $where_suchtext . $where_wertung_min
    . " ORDER BY jahr DESC, b.nummer" . $limit;

// echo $sql;

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows($resultat) > 0)
    {
        while ($datensatz = mysqli_fetch_assoc($resultat))
        {
            $anzahl ++;

            $bildinfo = ofa_GetBildPfad($datensatz["nummer"], $datensatz["ticket"], $datensatz["jahr"]);

            $bilddaten = "";

            if (strlen($bildinfo["pfad"]) > 0)
            {
                $bilddaten = '<a class=\"fancybox\" rel=\"group\" href=\"' . $bildinfo["pfad"] . '.jpg\" title=\"' . $datensatz["bildid"] . ' | ' . $datensatz["wertung"] . '\">'
                    . '<img id=\"img' . $datensatz["bildid"] . '\" class=\"mini\" src=\"' . $bildinfo["pfad"] . '.' . $bildinfo["extension"] . '\"></a><br>';
            } else if (strpos ($datensatz["beschreibung"], 'YOUTUBE') !== false) {
                $bilddaten = $datensatz["ort"] . '<br>';
            }

            $bilder .= '<li id=\"id' . $datensatz["bildid"] . '\" class=\"ui-state-default\">'
                . $datensatz["nummer"] . ' | '
                . '<span id=\"wertung' . $datensatz["bildid"] . '\">Wertung: ' . $datensatz["wertung"] . '</span>'
                . '<br>' . $bilddaten
                . '<a href=\"javascript:bild_setWertung(' . $datensatz["bildid"] . ',0)\">0</a> '
                . '<a href=\"javascript:bild_setWertung(' . $datensatz["bildid"] . ',1)\">1</a> '
                . '<a href=\"javascript:bild_setWertung(' . $datensatz["bildid"] . ',2)\">2</a> '
                . '<a href=\"javascript:bild_setWertung(' . $datensatz["bildid"] . ',3)\">3</a> '
                . '<a href=\"javascript:bild_setWertung(' . $datensatz["bildid"] . ',4)\">4</a> '
                . '<a href=\"javascript:bild_setWertung(' . $datensatz["bildid"] . ',5)\">5</a>'
                . '</li>';
        }
    }

    mysqli_free_result($resultat);
}

echo '{';
echo '  "anzahl": "' . $anzahl . '",';
echo '  "bilder": "' . $bilder . '"';
echo '}';

mysqli_close($db_link);
?>
