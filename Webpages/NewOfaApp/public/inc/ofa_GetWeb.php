<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$webid = 0 + $_GET["webid"];
// echo $sql;

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "SELECT w.web FROM $dbt_ofa_web w WHERE w.id=$webid";

if ($resultat = mysqli_query($db_link, $sql))
{
    $web = "";

    if (mysqli_num_rows($resultat) > 0)
    {
        $datensatz = mysqli_fetch_assoc($resultat);
        
        $web = $datensatz["web"];
    }

    mysqli_free_result($resultat);
}

$web_serien = "";
$web_anzahl = 0;

$sql = "SELECT ws.nr, ws.serieid, s.serie FROM $dbt_ofa_web_serie ws, $dbt_ofa_serie s WHERE ws.webid=$webid AND ws.serieid=s.id ORDER BY ws.nr";

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows($resultat) > 0)
    {
        while ($datensatz = mysqli_fetch_assoc($resultat))
        {
            $web_anzahl ++;
            $web_serien .= '<li id=\"' . $webid . '|' . $datensatz["serieid"] . '\" class=\"ui-state-default\">' . $datensatz["nr"] . '&nbsp;&nbsp;&nbsp;&nbsp;'
                . '<a href=\"javascript:web_editSerie(' . $webid . ',' . $datensatz["serieid"] . ')\">Edit</a>&nbsp;&nbsp;'
                . '<a href=\"javascript:web_deleteSerie(' . $webid . ',' . $datensatz["serieid"] . ')\">Del</a><br>'
                . '<b>' . $datensatz["serie"] . '</b></li>';
        }
    }

    mysqli_free_result($resultat);
}

echo '{';
echo '  "web": "' . $web . '",';
echo '  "web_anzahl": "' . $web_anzahl . '",';
echo '  "web_serien": "' . $web_serien . '"';
echo '}';

mysqli_close($db_link);
?>
