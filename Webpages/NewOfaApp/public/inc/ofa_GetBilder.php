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

if (strlen($suchtext) > 0) {
    $where_suchtext = " AND ";

    $pos = strpos($suchtext, "DATUM");

    if ($pos === false) {
        $pos = strpos($suchtext, " AND ");

        if ($pos === false) {
            $pos = strpos($suchtext, " OR ");

            if ($pos === false) {
                $where_suchtext .= "(beschreibung LIKE '%" . $suchtext . "%' OR bemerkung LIKE '%" . $suchtext . "%' OR info LIKE '%" . $suchtext . "%')";
            } else {
                $splits = explode(" OR ", $suchtext);
                $where_suchtext .= "(";

                for ($i = 0; $i < count($splits); $i++) {
                    if ($i > 0) {
                        $where_suchtext .= " OR ";
                    }

                    $where_suchtext .= "beschreibung LIKE '%" . $splits[$i] . "%' OR bemerkung LIKE '%" . $splits[$i] . "%' OR info LIKE '%" . $splits[$i] . "%'";
                }

                $where_suchtext .= ")";
            }
        } else {
            $splits = explode(" AND ", $suchtext);
            $where_suchtext .= "(";

            for ($i = 0; $i < count($splits); $i++) {
                if ($i > 0) {
                    $where_suchtext .= " AND ";
                }

                $where_suchtext .= "(beschreibung LIKE '%" . $splits[$i] . "%' OR bemerkung LIKE '%" . $splits[$i] . "%' OR info LIKE '%" . $splits[$i] . "%')";
            }

            $where_suchtext .= ")";
        }
    } else {
        $datum = explode("-", substr($suchtext, 5));
        $where_suchtext .= "DAY(ofa_bild.datum)='" . $datum[0] . "' AND MONTH(ofa_bild.datum)='" . $datum[1] . "'";
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

$bilder = array();

$sql = "SELECT b.id AS bildid, bd.Kamera, bd.objektiv, b.nummer, YEAR(b.datum) AS jahr, b.ticket, b.beschreibung, b.wertung, o.ort "
    . "FROM ofa_bild b LEFT JOIN ofa_bilddaten bd ON (b.datei = bd.BildNr), ofa_ort o WHERE b.ortid=o.id AND b.beschreibung NOT LIKE \"YOUTUBE%\" "
    . $where_bildtyp . $where_jahr . $where_ortid . $where_landid . $where_nummern . $where_suchtext . $where_wertung_min
    . " ORDER BY jahr DESC, b.nummer" . $limit;

// echo $sql;

if ($resultat = mysqli_query($db_link, $sql)) {
    if (mysqli_num_rows($resultat) > 0) {
        while ($datensatz = mysqli_fetch_assoc($resultat)) {
            $bildinfo = ofa_GetBildPfad($datensatz["nummer"], $datensatz["ticket"], $datensatz["jahr"]);

            $bilder[] = '|' . $datensatz["bildid"] . '|' . $datensatz["nummer"] . '|' . $datensatz["Kamera"] . '|' . $datensatz["objektiv"] . '|'
                . $datensatz["wertung"] . '|' . $bildinfo["pfad"] . '|' . $bildinfo["extension"];
            
            $anzahl ++;
        }
    }

    mysqli_free_result($resultat);
}

$sql = 'SELECT DISTINCT bd.Kamera FROM ofa_bild b LEFT JOIN ofa_bilddaten bd ON (b.datei = bd.BildNr), ofa_ort o WHERE bd.Kamera IS NOT NULL AND bd.Kamera!="" AND b.ortid=o.id '
    . $where_bildtyp . $where_jahr . $where_ortid . $where_landid . $where_nummern . $where_suchtext . $where_wertung_min
    . ' ORDER BY bd.Kamera';

$kameras = array();

if ($resultat = mysqli_query($db_link, $sql)) {
    if (mysqli_num_rows($resultat) > 0) {
        while ($datensatz = mysqli_fetch_assoc($resultat)) {
            $kameras[] = $datensatz["Kamera"];
        }
    }
}

// echo json_encode($kameras);
    
$sql = 'SELECT DISTINCT bd.objektiv FROM ofa_bild b LEFT JOIN ofa_bilddaten bd ON (b.datei = bd.BildNr), ofa_ort o WHERE bd.objektiv IS NOT NULL AND bd.objektiv!="" AND b.ortid=o.id '
    . $where_bildtyp . $where_jahr . $where_ortid . $where_landid . $where_nummern . $where_suchtext . $where_wertung_min
    . ' ORDER BY bd.objektiv';

$objektive = array();

if ($resultat = mysqli_query($db_link, $sql)) {
    if (mysqli_num_rows($resultat) > 0) {
        while ($datensatz = mysqli_fetch_assoc($resultat)) {
            $objektive[] = $datensatz["objektiv"];;
        }
    }
}

// echo json_encode($objektive);


echo '{';
echo '  "anzahl": "' . $anzahl . '",';
echo '  "bilder": ' . json_encode($bilder) . ',';
echo '  "kameras": ' . json_encode($kameras) . ',';
echo '  "objektive":' . json_encode($objektive);
echo '}';


mysqli_close($db_link);
?>
