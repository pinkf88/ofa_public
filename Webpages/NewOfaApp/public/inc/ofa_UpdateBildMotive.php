<?php
include_once "ofa_DbConsts.php";
include_once "ofa_Database.php";

$bildid = 0 + $_GET['bildid'];
$motive = explode('|', $_GET['motive']);

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "DELETE FROM $dbt_ofa_bild_motiv WHERE bildid=$bildid";
mysqli_query($db_link, $sql);

if (count($motive) > 1)
{
	for ($m = 0; $m < count($motive) - 1; $m++)
	{
		$default = 0;
		
		if ($m == 0)
			$default = 1;
		
		$sql = "INSERT $dbt_ofa_bild_motiv VALUES (NULL, " . $bildid . ", " . ($m + 1) . ", " . $motive[$m] . ", " . $default . ")";
		mysqli_query($db_link, $sql);
	}	
		
}

mysqli_close($db_link);
// echo 'Motive: ' . $_GET['motive'];
// print_r ($motive);
?>