<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";
include_once "ofa_Convenience.php";

$bildid = 0 + $_GET["bildid"];
$search = 0 + $_GET["search"];

// echo $sql;

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$ortid = "0";
$suchtext = "";
$bildmotive = "";
$motive = "";

$sql = "SELECT ortid, beschreibung, bemerkung FROM $dbt_ofa_bild WHERE id=$bildid";

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows($resultat) > 0)
    {
        $datensatz = mysqli_fetch_assoc($resultat);
        $ortid = $datensatz["ortid"];
        $suchtext = $datensatz["beschreibung"] . ' ' . $datensatz["bemerkung"];
    }
    else
    {
        mysqli_free_result($resultat);
        mysqli_close($db_link);
        return;
    }

    mysqli_free_result($resultat);
}

$sql = "SELECT id FROM $dbt_ofa_bild_motiv WHERE bildid=$bildid";

if ($resultat1 = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows($resultat1) == 0)
    {
        $sql = "SELECT m.id, m.motiv FROM $dbt_ofa_motiv m WHERE m.ortid=$ortid ORDER BY m.motiv";
        $resultat = mysqli_query($db_link, $sql);
        
        if (mysqli_num_rows($resultat) > 0)
        {
            $m = 1;
            $motive = '++NEU++';
            
            while ($datensatz = mysqli_fetch_assoc($resultat))
            {
                if (strpos($suchtext, $datensatz["motiv"]) !== false)
                {
                    $default = 0;
                    
                    if ($m == 1)
                        $default = 1;
                    
                    $sql = "INSERT $dbt_ofa_bild_motiv VALUES (NULL, " . $bildid . ", " . $m . ", " . $datensatz["id"] . ", " . $default . ")";
                    mysqli_query($db_link, $sql);
                    
                    $m++;
                }
            }
        }
    }
    else
    {
        // Dialogbox wird geöffnet werden
        $motivids = array();
        
        $sql = "SELECT m.id, m.motiv, bm.default FROM $dbt_ofa_motiv m, $dbt_ofa_bild_motiv bm WHERE bm.bildid=$bildid AND bm.motivid=m.id ORDER BY m.motiv";

        if ($resultat = mysqli_query($db_link, $sql))
        {
            if (mysqli_num_rows($resultat) > 0)
            {
                while ($datensatz = mysqli_fetch_assoc($resultat))
                {
                    $motivids[] = 0 + $datensatz["id"];
                    $bildmotive .= '<b> <a href=\"javascript:bild_motivOnClick(' . $bildid . ',' . $datensatz["id"] . ')\">' . $datensatz["motiv"] . '</a></b><br>';
                }
            }

            mysqli_free_result($resultat);
        }
        
        $motive = "";
        
        $sql = "SELECT m.id, m.motiv FROM $dbt_ofa_motiv m WHERE m.ortid=$ortid ORDER BY m.motiv";

        if ($resultat = mysqli_query($db_link, $sql))
        {
            if (mysqli_num_rows($resultat) > 0)
            {
                while ($datensatz = mysqli_fetch_assoc($resultat))
                {
                    $checked = '';
                    
                    if (in_array($datensatz["id"], $motivids))
                    {
                        $checked = 'checked=\"checked\"';
                    }
                    else if (($search == 1) && (strpos($suchtext, $datensatz["motiv"]) !== false))
                    {
                        $checked = 'checked=\"checked\"';
                        $bildmotive .= '<b>' . $datensatz["motiv"] . '</b><br>';
                        
                        $sql = "INSERT $dbt_ofa_bild_motiv VALUES (NULL, " . $bildid . ", 0, " . $datensatz["id"] . ", 0)";
                        mysqli_query($db_link, $sql);
                    }
                    
                    $motive .= '<input type=\"checkbox\" name=\"motiv' . $datensatz["id"] . '\" ' . $checked . ' onclick=\"bild_motivOnClick(' . $bildid . ', 0)\"><label class=\"motiv\">' . addcslashes($datensatz["motiv"], '"') . '</label><br>';
                }
            }

            mysqli_free_result($resultat);
        }
    }

    mysqli_free_result($resultat1);
}

// <input type="checkbox" name="motiv2623#Abflugshalle Flughafen Riem" value="1">

echo '{';
echo '  "bildmotive": "' . $bildmotive . '",';
echo '  "motive": "' . $motive . '"';
echo '}';

mysqli_close($db_link);
?>
