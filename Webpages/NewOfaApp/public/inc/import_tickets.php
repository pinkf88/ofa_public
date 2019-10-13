<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "SELECT ID, Datum, YEAR(Datum) AS Jahr, TO_DAYS(Datum) AS Tage, Nr, OrtID, Beschreibung, Bemerkung FROM ticket ORDER BY Datum, Nr";
echo $sql . '<br>';

$resultat = mysql_query($sql);

if (mysql_num_rows($resultat) > 0)
{
    while ($datensatz = mysql_fetch_assoc($resultat))
    {
        $jahr = 0;    
    
        if (0 + $datensatz['Jahr'] < 2000)
            $jahr = $datensatz['Jahr'] - 1900;
        else
            $jahr = $datensatz['Jahr'] - 2000;
        
        $nummer = $jahr * 100000 + 90000 + $datensatz['ID'];
        $datei = $datensatz['Tage'] * 100 + $datensatz['Nr'];

        printf ("id: %d, datum: %s, nummer: %d, datei: %d<br>", 0 + $datensatz['ID'], $datensatz['Datum'], $nummer, $datei);
                
        $sql = "INSERT $dbt_ofa_bild VALUES (NULL, " . $nummer . "," . $datei . ",\"" . $datensatz['Beschreibung'] . "\","
                . $datensatz['OrtID'] . ",\"" . $datensatz['Datum'] . "\",0,\"" . $datensatz['Bemerkung'] . "\",0,1)";
        echo $sql . '<br>';
        mysql_query($sql);
    }
}
?>
