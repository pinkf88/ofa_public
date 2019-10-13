<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$lebenid = 0 + $_GET['lebenid'];
$direction = 0 + $_GET['direction'];
$result = 0;

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$datumvon = "";
$nr = 0;

$sql = "SELECT l.datumvon, l.nr FROM $dbt_ofa_leben l WHERE l.id=$lebenid";

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows($resultat) > 0)
    {
        $datensatz = mysqli_fetch_assoc($resultat);

        $datumvon = $datensatz["datumvon"];
        $nr = 0 + $datensatz["nr"];
    }

    mysqli_free_result($resultat);
}

if ($direction == 1)    // up
{
    if ($nr > 1)
    {
        $sql = "UPDATE $dbt_ofa_leben l SET l.nr=$nr WHERE l.datumvon='$datumvon' AND l.nr=" . ($nr - 1);
        mysqli_query($db_link, $sql);
        
        $sql = "UPDATE $dbt_ofa_leben l SET l.nr=" . ($nr - 1) . " WHERE l.id=$lebenid";
        mysqli_query($db_link, $sql);
        
        $result = 1;
    }
}
else    // down
{
    $maxnr = 0;

    $sql = "SELECT MAX(l.nr) AS maxnr FROM $dbt_ofa_leben l WHERE l.datumvon='$datumvon'";
    
    if ($resultat = mysqli_query($db_link, $sql))
    {
        if (mysqli_num_rows($resultat) > 0)
        {
            $datensatz = mysqli_fetch_assoc($resultat);
        
            $maxnr = 0 + $datensatz["maxnr"];
        }

        mysqli_free_result($resultat);
    }
    
    if ($nr < $maxnr)
    {
        $sql = "UPDATE $dbt_ofa_leben l SET l.nr=$nr WHERE l.datumvon='$datumvon' AND l.nr=" . ($nr + 1);
        mysqli_query($db_link, $sql);
        
        $sql = "UPDATE $dbt_ofa_leben l SET l.nr=" . ($nr + 1) . " WHERE l.id=$lebenid";
        mysqli_query($db_link, $sql);
        
        $result = 1;
    }
}

echo '{';
echo '"result": "' . $result . '"';
echo '}';

mysqli_close($db_link);
?>
