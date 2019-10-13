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
$ticket = 0;
$jahr = 0;
$info = "";
$aufnahmedatum = "";
$polygon = "";
$geodaten = "";

$sql = "SELECT nummer, datei, info, ticket, YEAR(datum) as jahr FROM $dbt_ofa_bild WHERE id=$bildid";

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows ($resultat) > 0)
    {
        $datensatz = mysqli_fetch_assoc ($resultat);
        $nummer = $datensatz["nummer"];
        $datei = $datensatz["datei"];
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

    if ($resultat = mysqli_query($db_link, $sql))
    {
        if (mysqli_num_rows ($resultat) > 0)
        {
            $motivliste .= '| ';

            while ($datensatz = mysqli_fetch_assoc ($resultat))
            {
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

    $sql = "SELECT ws.titel FROM $dbt_ofa_serie_bild sb, $dbt_ofa_web_serie ws WHERE sb.bildid=$bildid AND sb.serieid=ws.serieid ORDER BY ws.titel";

    if ($resultat = mysqli_query($db_link, $sql))
    {
        if (mysqli_num_rows ($resultat) > 0)
        {
            while ($datensatz = mysqli_fetch_assoc ($resultat))
            {
                $serieliste .= '<b><i>' . $datensatz["titel"] . '</i></b><br>';
            }
        }

        mysqli_free_result($resultat);
    }

    $serieliste .= '</p>';

    if ($info != "")
    {
        $zusatzinfo = '<p><i>Info: ' . $info . '</i></p>';
    }
}

$bildinfo = ofa_getBildPfad(0 + $nummer, $ticket, $jahr);

// print_r($bildinfo);

if ($version == 1) {
    if (strlen($bildinfo["pfad"]) > 0)
    {
        $bilddaten = '<a class=\"fancybox\" rel=\"group\" href=\"' . $bildinfo["pfad"] . '.jpg\">'
            . '<img class=\"mini\" src=\"' . $bildinfo["pfad"] . '.' . $bildinfo["extension"] . '\"></a><br>';
    }

    $nummer = '<p><b>' . $nummer . '</b>';

    $sql = "SELECT bd.BildNr, bd.Aufnahmedatum, bd.Kameradatum, bd.Laenge, bd.Breite, bd.polygon, bd.locations FROM $dbt_ofa_bilddaten bd WHERE bd.BildNr=$datei";

    if ($resultat = mysqli_query($db_link, $sql))
    {
        if (mysqli_num_rows ($resultat) > 0)
        {
            $datensatz = mysqli_fetch_assoc ($resultat);

            if ($datensatz["Aufnahmedatum"] <> "")
            {
                if ($datensatz["Aufnahmedatum"] == '0000-00-00 00:00:00')
                {
                    $aufnahmedatum = '<p class=\"aufnahmedatum\">' . $datensatz["Kameradatum"] . '</p>';
                }
                else
                {
                    $aufnahmedatum = '<p class=\"aufnahmedatum\">' . $datensatz["Aufnahmedatum"] . '</p>';
                }
            }

            if ($datensatz["Laenge"] <> 0 && $datensatz["Breite"] <> 0) {
                $breite = (float)$datensatz["Breite"] / 10000.0;
                $laenge = (float)$datensatz["Laenge"] / 10000.0;

                $geodaten = '<p class=\"geodaten\">' . $breite . '° / ' . $laenge . '°&nbsp;&nbsp;';
                $geodaten .= '<a class=\"geodel\" href=\"javascript:bild_deleteGeodaten(' . $bildid . ', ' . $datei . ')\">Löschen</a>&nbsp;&nbsp;';
                $geodaten .= '<a class=\"geocopy\" href=\"javascript:bild_copyGeodaten(' . $datensatz["Breite"] . ', ' . $datensatz["Laenge"] . ')\">Kopieren</a></p>';
            } else {
                $geodaten .= '<p class=\"geodaten\"><a class=\"geoupdate\" href=\"javascript:bild_updateGeodaten(' . $bildid . ', ' . $datei . ')\">Update Geodaten</a></p>';
            }

            if ($datensatz["polygon"] != null || $datensatz["polygon"] <> "")
            {
                $polygon = 'Polygon';

                if ($datensatz["locations"] != null || $datensatz["locations"] <> "")
                {
                    $polygon .= ' | Locations';
                }
            }
        }

        mysqli_free_result($resultat);
    }

    $nummer .= '</p>';

    echo '{';
    echo '  "nummer": "' . $nummer . '",';
    echo '  "motivliste": "' . $motivliste . '",';
    echo '  "serieliste": "' . $serieliste . '",';
    echo '  "zusatzinfo": "' . $zusatzinfo . '",';
    echo '  "aufnahmedatum": "' . $aufnahmedatum . '",';
    echo '  "geodaten": "' . $geodaten . '",';
    echo '  "bilddaten": "' . $bilddaten . '",';
    echo '  "polygon": "' . $polygon .  '"';
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
