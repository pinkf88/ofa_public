<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "SELECT id, serieid, nr, Datei, zusatz FROM serie_bild ORDER BY serieid, nr";
// echo $sql . '<br>';

$resultat = mysql_query($sql);

if (mysql_num_rows($resultat) > 0)
{
    while ($datensatz = mysql_fetch_assoc($resultat))
    {
        $nummer = "";
        $bildid = 0;
        $sql2 = "";
        
        if (strpos($datensatz['Datei'], 'T') === false)
        {
            $temp = 0 + $datensatz['Datei'];
            $nummer = "" + $temp;
            $sql2 = "SELECT id FROM $dbt_ofa_bild WHERE nummer=$nummer";
        }
        else
        {
            $nummer = substr($datensatz['Datei'], 3);
            $sql2 = "SELECT id FROM $dbt_ofa_bild WHERE RIGHT(nummer,4)=\"" . $nummer . "\" AND ticket=1";
        }
        
        $resultat2 = mysql_query($sql2);
        
        if (mysql_num_rows($resultat) > 0)
        {
            $datensatz2 = mysql_fetch_assoc($resultat2);
            
            $bildid = 0 + $datensatz2['id'];
        }
        
        
        printf ("id: %d, serieid: %d, nr: %d, datei: %s / %s, bildid: %d<br>", 0 + $datensatz['id'], 0 + $datensatz['serieid'], 0 + $datensatz['nr'], 
            $datensatz['Datei'], $nummer, $bildid);
        
        if ($bildid > 0)
        {
            $sql = "INSERT $dbt_ofa_serie_bild VALUES (NULL, " . $datensatz['serieid'] . "," . $datensatz['nr'] . ","
                    . $bildid . ",\"" . addslashes($datensatz['zusatz']) . "\")";
//             echo $sql . '<br>';
            mysql_query($sql);
        }
    }
}
?>
