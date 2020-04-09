<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

// var_dump($_POST["bilder"]);

$bilddaten = json_decode($_POST["bilder"], true);
// var_dump($bilddaten["bilder"]);

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$nummer = $bilddaten["nummer"];

for ($i = 0; $i < count($bilddaten["bilder"]); $i++)
{
    $sql = 'INSERT INTO ofa_bild (nummer, datei, datum, ortid, beschreibung, bemerkung) '
        . 'VALUES ("' . $nummer . '", "' . $bilddaten["bilder"][$i]["datei"] . '", '
        . '"' . $bilddaten["bilder"][$i]["datum"] . '", "' . $bilddaten["ortid"] . '", '
        . '"' . $bilddaten["bilder"][$i]["beschreibung"] . '", "' . $bilddaten["bilder"][$i]["bemerkung"] . '");';

    // echo $sql;
    mysqli_query($db_link, $sql);

    $nummer++;
}

echo '{';
echo '  "nummer_start": ' . $bilddaten["nummer"] . ',';
echo '  "nummer_ende": ' . ($nummer - 1);
echo '}';
    
mysqli_close($db_link);
?>
