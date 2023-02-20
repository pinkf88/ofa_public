<!DOCTYPE html>
<!-- ÄÖÜäöü -->
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Bilder - OneForAll</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Le styles -->
        <link href="/css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css">
<link href="/css/bootstrap-theme.min.css" media="screen" rel="stylesheet" type="text/css">
<link href="/css/style.css" media="screen" rel="stylesheet" type="text/css">
<link href="/css/jquery-ui.css" media="screen" rel="stylesheet" type="text/css">
<link href="/css/ofa.css" media="screen" rel="stylesheet" type="text/css">
<link href="/img/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon">
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
<?php
include_once "inc/ofa_DbConsts.php";
include_once "inc/ofa_Database.php";
include_once "inc/ofa_Convenience.php";

$bildid = 0 + $_GET['bildid'];
$nummer = "0";
$datei = "0";

$db_link = ofa_db_connect($db_ofa_server, $db_ofa_user, $db_ofa_password, $db_ofa_database);

$sql = "SELECT nummer, datei, YEAR(datum) as jahr FROM $dbt_ofa_bild WHERE id=$bildid";
// echo $sql;

if ($resultat = mysqli_query($db_link, $sql)) {
    if (mysqli_num_rows ($resultat) > 0) {
        $datensatz = mysqli_fetch_assoc ($resultat);
        $nummer = $datensatz["nummer"];
        $datei = $datensatz["datei"];
        $bildinfo = ofa_getBildPfad(0 + $nummer, 0, 0 + $datensatz["jahr"]);
    }

    mysqli_free_result($resultat);
}

$laenge = 0.0;
$breite = 0.0;
$polygon = '';
// $bildinfo["pfad"] = "https://www.juergen-reichmann.de/images/pics/1701000/1701632.jpg";

$sql = "SELECT bd.BildNr, bd.Aufnahmedatum, bd.Laenge, bd.Breite, bd.polygon FROM $dbt_ofa_bilddaten bd WHERE bd.BildNr=$datei";
// echo $sql;

if ($resultat = mysqli_query($db_link, $sql))
{
    if (mysqli_num_rows ($resultat) > 0)
    {
        $datensatz = mysqli_fetch_assoc ($resultat);
        
        if ($datensatz["Laenge"] <> 0 && $datensatz["Breite"] <> 0)
        {
            $laenge = (float)number_format((float)((int)$datensatz["Laenge"] / 10000.0), 4);
            $breite = (float)number_format((float)((int)$datensatz["Breite"] / 10000.0), 4);
        }

        if (strlen($datensatz["polygon"]) > 0)
        {
            $polygon = $datensatz["polygon"];
        }

    }

    mysqli_free_result($resultat);
}

if ($polygon == '')
{
    $polygon = "[[" . ($laenge - 0.01) . "," . ($breite - 0.005) . "],"
        . "[" . ($laenge - 0.01) . "," . ($breite + 0.005) . "],"
        . "[" . ($laenge + 0.01) . "," . ($breite + 0.005) . "],"
        . "[" . ($laenge + 0.01) . "," . ($breite - 0.005) . "]]";
}

echo '<script type="text/javascript">' . "\n";
echo 'var bildnr = ' .$datensatz["BildNr"] . ';' . "\n";
echo 'var breite = ' . $breite . ';' . "\n";
echo 'var laenge = ' . $laenge . ';' . "\n";
echo 'var zoom = 15;' . "\n";
echo 'var polygon = "' . $polygon . '";' . "\n";
// echo 'var polygon = "[[11.56069,48.14856],[11.57065,48.14999],[11.57908,48.14238],[11.55554,48.13783]]";' . "\n";
echo 'var img_src = "' . $bildinfo["pfad"] . '.jpg";' . "\n";
echo '</script>' . "\n";

mysqli_close($db_link);
?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key="></script>
<script type="text/javascript" src="/js/ofa_bild_poly.js"></script>
    </head>
    <body>
        <div id="wrapper">
            <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="/">OneForAll</a>
                    </div>
                    <div class="collapse navbar-collapse">
                        <ul class="nav navbar-nav">
    <li class="active"><a href="/">Startseite</a></li><li class="active"><a href="/leben">Leben</a></li><li class="active"><a href="/bild">Bilder</a></li><li class="active"><a href="/serie">Serien</a></li><li class="active"><a href="/web">Web</a></li><li class="active"><a href="/land">Länder</a></li><li class="active"><a href="/ort">Orte</a></li><li class="active"><a href="/motiv">Motive</a></li>                        </ul>
                    </div><!--/.nav-collapse -->
                </div>
            </nav>

            <div class="container">
                <div class="row margin0">
                    <div class="col-md-5">
                        <canvas id="canvas" width="1000" height="1000" style="border: 1px solid red;"></canvas><br>
                        <button id="clockwise" onclick="drawRotated(90)">Rotate right</button>
                        <button id="counterclockwise" onclick="drawRotated(-90)">Rotate left</button>
                    </div>
                    <div class="col-md-7">
                        <div id="jr_map" class="bild_map" style="height: 1000px;"></div>
                        <div class="vspace_klein"></div>
                        <div style="float: left; margin-right: 20px;">
                            <textarea id="info" style="height: 100px; width: 500px; color: black;"></textarea>
                        </div>
                        <div>
                            <button id="clipboard-btn" onclick="saveToDatabase()">Copy to database</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
