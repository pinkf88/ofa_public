<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$ortid = 0 + $_GET["ortid"];

// echo $sql;

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$motive = "";
$m = 0;
    
$sql = "SELECT m.motiv FROM $dbt_ofa_motiv m WHERE m.ortid=$ortid ORDER BY m.motiv";

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows($resultat) > 0)
    {
        while ($datensatz = mysqli_fetch_assoc($resultat))
        {
            if ($m > 0)
                $motive .= ", ";
            
            $motive .= '"' . $datensatz["motiv"] . '"';
            $m++;
        }
    }

    mysqli_free_result($resultat);
}

echo '{';
echo '  "motive": [' . $motive . ']' . "\n";
echo '}';

mysqli_close($db_link);
?>
