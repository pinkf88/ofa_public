<?php

include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$tag = 0 + $_GET["tag"];
$monat = 0 + $_GET["monat"];
$jahr = 0 + $_GET["jahr"];

$jahr_min = 1967;

// echo $sql;

function date_mysql2german($datum)
{
    $d = explode("-", $datum);
    
    return sprintf("%02d.%02d.%04d", $d[2], $d[1], $d[0]);
}

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "SELECT l.id, l.datumvon, l.datumbis, o.ort, la.land, l.beschreibung, l.bemerkung FROM $dbt_ofa_leben l, $dbt_ofa_ort o, $dbt_ofa_land la ";
$sql .= "WHERE l.ortid=o.id AND o.landid=la.id AND (";

for($i = $jahr_min; $i <= $jahr; $i ++)
{
    if ($i != $jahr_min)
    {
        $sql .= 'OR ';
    }
    
    $sql .= '("' . sprintf("%'04d-%'02d-%'02d", $i, $monat, $tag) . '" BETWEEN l.datumvon AND l.datumbis) ';
    
    // s := s + '(DatumVon BETWEEN ("' + Format ('%4.4d-%2.2d-%2.2d', [i, wMonat, wTag]) + '" - INTERVAL 3 DAY) '
    // + 'AND ("' + Format ('%4.4d-%2.2d-%2.2d', [i, wMonat, wTag]) + '" + INTERVAL 3 DAY)) ';
    // end;
    // end;

}

$sql .= ') ORDER BY YEAR(l.datumvon) DESC, l.datumvon ASC, l.nr ASC';

$tabelle = '<table class="table">';

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows($resultat) > 0)
    {
        while ($datensatz = mysqli_fetch_assoc($resultat))
        {
            $tabelle .= '<tr class="firstline"><td class="datum">' . date_mysql2german($datensatz["datumvon"]);
            
            if ($datensatz["datumvon"] != $datensatz["datumbis"])
            {
                $tabelle .= ' - ' . date_mysql2german($datensatz["datumbis"]);
            }
            
            $tabelle .= '</td><td>' . $datensatz["ort"] . '</td><td>' . $datensatz["land"] . '</td><td>' . $datensatz["beschreibung"] . '</td></tr>';
            
            if ($datensatz["bemerkung"] != '')
            {
                $tabelle .= '<tr class="secondline">';
                $tabelle .= '<td colspan="3" style="border-top: 0px; padding-top: 0px;"></td>';
                $tabelle .= '<td style="border-top: 0px; padding-top: 0px;">' . $datensatz["bemerkung"] . '</td>';
                $tabelle .= '</tr>';
            }
            
        }
    }

    mysqli_free_result($resultat);
}

$tabelle .= '</table>';

mysqli_close($db_link);

echo $tabelle;
?>
