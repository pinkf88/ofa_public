<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$serieid = 0 + $_GET["serieid"];

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$serie = "";

$sql = "SELECT s.serie FROM $dbt_ofa_serie s WHERE s.id=$serieid";
// echo $sql;

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows($resultat) > 0)
    {
        $datensatz = mysqli_fetch_assoc($resultat);

        $serie = $datensatz["serie"];
    }

    mysqli_free_result($resultat);
}

$serie_bilder = "";
$serie_anzahl = 0;

$sql = "SELECT sb.nr, sb.bildid, sb.dauer, b.nummer, YEAR(b.datum) AS jahr, b.ticket, b.beschreibung, o.ort "
    . "FROM $dbt_ofa_serie_bild sb, $dbt_ofa_bild b, $dbt_ofa_ort o WHERE sb.serieid=$serieid AND sb.bildid=b.id AND b.ortid=o.id ORDER BY sb.nr";
// echo $sql;

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows($resultat) > 0)
    {
        while ($datensatz = mysqli_fetch_assoc($resultat))
        {
            $serie_anzahl ++;

            $bildinfo = ofa_GetBildPfad($datensatz["nummer"], $datensatz["ticket"], $datensatz["jahr"]);

            $bilddaten = "";

            if (strlen($bildinfo["pfad"]) > 0)
            {
                $bilddaten = '<a class=\"fancybox\" rel=\"group\" href=\"' . $bildinfo["pfad"] . '.jpg\"><img class=\"mini\" src=\"' . $bildinfo["pfad"] . '.' . $bildinfo["extension"] . '\"></a><br>';
            } else if (strpos ($datensatz["beschreibung"], 'YOUTUBE') !== false) {
                $bilddaten = $datensatz["ort"] . '<br>';
            }

            $serie_bilder .= '<li id=\"id' . $datensatz["bildid"] . '\" class=\"ui-state-default\">'
                . $datensatz["nr"] . ' | ' . $datensatz["nummer"] . ' | '
                . '<span id=\"dur' . $datensatz["bildid"] . '\">' . $datensatz["dauer"] . ' Sekunden</span>'
                . '<br>' . $bilddaten
                . '<a href=\"javascript:serie_editBild(' . $serieid . ',' . $datensatz["bildid"] . ')\">Edit</a>&nbsp;&nbsp;'
                . '<a href=\"javascript:serie_deleteBild(' . $serieid . ',' . $datensatz["bildid"] . ')\">Del</a>&nbsp;&nbsp;|&nbsp;&nbsp;'
                . '<a href=\"javascript:serie_setDauer(' . $serieid . ',' . $datensatz["bildid"] . ',2)\">2</a> '
                . '<a href=\"javascript:serie_setDauer(' . $serieid . ',' . $datensatz["bildid"] . ',5)\">5</a> '
                . '<a href=\"javascript:serie_setDauer(' . $serieid . ',' . $datensatz["bildid"] . ',8)\">8</a> '
                . '<a href=\"javascript:serie_setDauer(' . $serieid . ',' . $datensatz["bildid"] . ',15)\">15</a> '
                . '<a href=\"javascript:serie_setDauer(' . $serieid . ',' . $datensatz["bildid"] . ',30)\">30</a>'
                . '</li>';
        }
    }

    mysqli_free_result($resultat);
}

echo '{';
echo '  "serie": "' . $serie . '",';
echo '  "serie_anzahl": "' . $serie_anzahl . '",';
echo '  "serie_bilder": "' . $serie_bilder . '"';
echo '}';

mysqli_close($db_link);
?>
