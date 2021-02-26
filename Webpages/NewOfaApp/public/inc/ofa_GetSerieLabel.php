<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$serieid = 0 + $_GET["serieid"];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$serie = "";

$sql = "SELECT s.serie FROM $dbt_ofa_serie s WHERE s.id=$serieid";

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows($resultat) > 0)
    {
        $datensatz = mysqli_fetch_assoc($resultat);

        $serie = $datensatz["serie"];
    }

    mysqli_free_result($resultat);
}

$sql = "SELECT DISTINCT l.id AS labelid, l.label_de, l.label_en "
    . "FROM ofa_serie_bild sb, ofa_bild b LEFT JOIN ofa_bild_label bl ON (b.id = bl.bildid) JOIN ofa_label l ON (bl.labelid=l.id) "
    . "WHERE sb.serieid=$serieid AND sb.bildid=b.id AND bl.valid=1 AND l.used=1 ORDER BY l.label_de";

$serie_labels = '';

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows($resultat) > 0)
    {
        while ($datensatz = mysqli_fetch_assoc($resultat))
        {
            $serie_labels .= '<span id=\"label' . $datensatz["labelid"] . '\">'
                . '<a href=\"javascript:serie_invalidLabelForSerie(' . $serieid . ',' . $datensatz["labelid"] . ')\">'
                . '<b>' . $datensatz["label_de"] . ' / ' . $datensatz["label_en"] . '</b></a>'
                . ' | <a href=\"javascript:serie_showBilderWithLabel(' . $datensatz["labelid"] . ')\">Show</a>'
                . ' | <a href=\"javascript:serie_showBilderWithLabel(0)\">All</a>'
                . '</span><br>';
        }
    }

    mysqli_free_result($resultat);
}

$serie_bilder = '';
$serie_anzahl = 0;
$nummer_alt = '';

/*
$sql = "SELECT DISTINCT b.id AS bildid, b.nummer, YEAR(b.datum) AS jahr, b.ticket, lb.label_en, lb.label_de, bl.score, bl.version, bl.valid "
. "FROM ofa_serie_bild sb, ofa_bild b LEFT JOIN ofa_bild_label bl ON (b.id = bl.bildid) LEFT JOIN ofa_label lb ON (bl.labelid=lb.id) "
. "WHERE sb.serieid=$serieid AND sb.bildid=b.id ORDER BY b.nummer, bl.version DESC, bl.score DESC";
*/

$sql = "SELECT DISTINCT sb.nr, b.id AS bildid, b.nummer, YEAR(b.datum) AS jahr, b.ticket, b.beschreibung, b.bemerkung, o.ort, bl.version "
    . "FROM $dbt_ofa_serie_bild sb, $dbt_ofa_bild b LEFT JOIN ofa_bild_label bl ON (b.id = bl.bildid), $dbt_ofa_ort o "
    . "WHERE sb.serieid=$serieid AND sb.bildid=b.id AND b.ortid=o.id "
    . "ORDER BY sb.nr, bl.version DESC";

// echo $sql;

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows($resultat) > 0)
    {
        while ($datensatz = mysqli_fetch_assoc($resultat))
        {
            if ($nummer_alt != $datensatz["nummer"]) {
                $nummer_alt = $datensatz["nummer"];
                $serie_anzahl ++;

                $bildinfo = ofa_GetBildPfad($datensatz["nummer"], $datensatz["ticket"], $datensatz["jahr"]);
                $labels = '| ';
                $label_ids = 'id_';

                if ($datensatz["version"] > 0) {
                    $sql = "SELECT DISTINCT bl.id AS bildlabelid, lb.id AS labelid, lb.label_en, lb.label_de, bl.score, bl.valid "
                    . "FROM $dbt_ofa_bild_label bl, $dbt_ofa_label lb "
                    . "WHERE bl.labelid=lb.id AND bl.bildid=" . $datensatz['bildid'] . " AND bl.version=" . $datensatz["version"] . " AND lb.used=1 "
                    . "ORDER BY bl.score DESC";

                    // echo $sql;

                    if ($resultat_label = mysqli_query($db_link, $sql))
                    {
                        if (mysqli_num_rows($resultat_label) > 0)
                        {
                            while ($datensatz_label = mysqli_fetch_assoc($resultat_label))
                            {
                                $label = $datensatz_label['label_de'] . ' / ' . $datensatz_label['label_en'] . ' (' . $datensatz_label['score'] . ')';

                                $aclass = '';
                                $jscall = 'serie_invalidLabel';

                                if ($datensatz_label['valid'] == 0) {
                                    $aclass = ' class=\"red\"';
                                    $jscall = 'serie_validLabel';
                                }

                                $labels .= '<span id=\"bl' . $datensatz_label["bildlabelid"] . '\"><a' . $aclass . ' href=\"javascript:' . $jscall . '(' . $datensatz_label["bildlabelid"] . ')\">' . $label . '</a></span>';
                                $labels .= ' | ';

                                $label_ids .= $datensatz_label["labelid"] . '_';
                            }
                        }
                    }

                    mysqli_free_result($resultat_label);
                }

                if ($labels == '| ') {
                    $labels = '';
                }

                if ($label_ids == 'id_') {
                    $label_ids = '';
                }

                $bilddaten = "";

                if (strlen($bildinfo["pfad"]) > 0)
                {
                    $bilddaten = '<div class=\"bildertable_bild\"><a class=\"fancybox\" rel=\"group\" href=\"' . $bildinfo["pfad"] . '.jpg\">'
                         . '<img class=\"mini\" src=\"' . $bildinfo["pfad"] . '.' . $bildinfo["extension"] . '\"></a></div>';
                }

                $serie_bilder .= '<li id=\"' .  $label_ids . '\" class=\"bildertable_element\">' . $bilddaten
                    . '<div class=\"bildertable_label\"><span class=\"bildertable_nummer\">' . $datensatz["nr"] . ' - ' . $datensatz["nummer"] . '</span>'
                    . '<span class=\"bildertable_beschreibung\"> - ' . $datensatz["ort"];

                if ($datensatz["beschreibung"] != "") {
                    $serie_bilder .= ' - ' . ofa_replace($datensatz["beschreibung"]);
                }

                if ($datensatz["bemerkung"] != "") {
                    $serie_bilder .= ' - ' . ofa_replace($datensatz["bemerkung"]);
                }

                $serie_bilder .= '<br><br></span>' . $labels . '</div></li>';
            }
        }
    }

    mysqli_free_result($resultat);
}

echo '{';
echo '  "serie": "' . $serie . '",';
echo '  "serie_anzahl": "' . $serie_anzahl . '",';
echo '  "serie_bilder": "' . $serie_bilder . '",';
echo '  "serie_labels": "' . $serie_labels . '"';
echo '}';

mysqli_close($db_link);
?>
