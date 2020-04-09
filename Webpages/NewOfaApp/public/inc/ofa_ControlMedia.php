<?php
// http://phphttpclient.com/
// sudo apt-get install php7.0-curl (sudo apt-get install php-curl)
// php.ini

include('./httpful.phar');

$type = $_GET["type"];

if ($type == 'audio') {
    // Audio
    if (isset($_GET["music"])) {
        $url = 'localhost:8085/audio?caller=browser'
            . '&music=' . $_GET["music"]
            . '&artistid=' . $_GET["artistid"]
            . '&year_from=' . $_GET["year_from"]
            . '&year_to=' . $_GET["year_to"]
            . '&runtype=2'
            . '&roomid=' . $_GET["roomid"]
            . '&personid=' . $_GET["personid"];

        $response = \Httpful\Request::get($url)->send();
    }
} else if ($type == 'track') {
    // Track
    if (isset($_GET["trackid"])) {
        $response = \Httpful\Request::get('localhost:8085/audio?trackid=' . $_GET["trackid"] . '&runmode=' . $_GET["runmode"])->send();
    }
} else if ($type == 'album') {
    if (isset($_GET["albumid"]) && isset($_GET["runtype"])) {
        // Start playing album
        $response = \Httpful\Request::get('localhost:8085/audio?albumid=' . $_GET["albumid"] . '&runtype=' . $_GET["runtype"])->send();
    } else if (isset($_GET["albumid"])) {
        // Start playing album
        $response = \Httpful\Request::get('localhost:8085/audio?albumid=' . $_GET["albumid"])->send();
    } else if (isset($_GET["info"])) {
        $response = \Httpful\Request::get('localhost:8085/audio?info')->send();
    } else if (isset($_GET["goto"])) {
        $response = \Httpful\Request::get('localhost:8085/audio?goto=' . $_GET["goto"])->send();
    } else if (isset($_GET["stop"])) {
        $response = \Httpful\Request::get('localhost:8085/audio?stop')->send();
    } else if (isset($_GET["pause"])) {
        $response = \Httpful\Request::get('localhost:8085/audio?pause')->send();
    } else if (isset($_GET["artist"]) && isset($_GET["runtype"])) {
        // Start playing artist
        $response = \Httpful\Request::get('localhost:8085/audio?artist=' . $_GET["artist"] . '&runtype=' . $_GET["runtype"])->send();
    }
} else if ($type == 'playlist') {
    if (isset($_GET["play"])) {
        // $response = \Httpful\Request::get('localhost:8085/playlist?play&trackids=' . $_GET["trackids"] . '&runtype=' . $_GET["runtype"])->send();
        $response = \Httpful\Request::post('localhost:8085/playlist?play&runtype=' . $_GET["runtype"], $_POST["trackids"], \Httpful\Mime::PLAIN)->send();
    } else if (isset($_GET["save"])) {
        $response = \Httpful\Request::post('localhost:8085/playlist?save&playlist_name=' . $_GET["playlist_name"], $_POST["trackids"], \Httpful\Mime::PLAIN)->send();
    } else if (isset($_GET["read"])) {
        $response = \Httpful\Request::get('localhost:8085/playlist?read&playlist_name=' . $_GET["playlist_name"])->send();
    } else if (isset($_GET["list"])) {
        $response = \Httpful\Request::get('localhost:8085/playlist?list')->send();
    } else if (isset($_GET["delete"])) {
        $response = \Httpful\Request::get('localhost:8085/playlist?delete&playlist_name=' . $_GET["playlist_name"])->send();
    } else if (isset($_GET["copy"])) {
        $response = \Httpful\Request::get('localhost:8085/playlist?copy&playlist_name=' . $_GET["playlist_name"])->send();
    }
} else if ($type == 'series') {
    if (isset($_GET["serieid"]) && isset($_GET["runtype"])) {
        $response = \Httpful\Request::get('localhost:8085/series?serieid=' . $_GET["serieid"] . '&runtype=' . $_GET["runtype"])->send();
    } else if (isset($_GET["info"])) {
        $response = \Httpful\Request::get('localhost:8085/series?info')->send();
    } else if (isset($_GET["goto"])) {
        $response = \Httpful\Request::get('localhost:8085/series?goto=' . $_GET["goto"])->send();
    } else if (isset($_GET["stop"])) {
        $response = \Httpful\Request::get('localhost:8085/series?stop')->send();
    } else if (isset($_GET["pause"])) {
        $response = \Httpful\Request::get('localhost:8085/series?pause')->send();
    }
} else if ($type == 'manage') {
    if (isset($_GET["audio_new"])) {
        $response = \Httpful\Request::get('localhost:8085/manage?audio=db_new')->send();
    } else if (isset($_GET["audio_update"])) {
        $response = \Httpful\Request::get('localhost:8085/manage?audio=db_update')->send();
    } else if (isset($_GET["picture"])) {
        $response = \Httpful\Request::get('localhost:8085/manage?picture=db')->send();
    }
} else if ($type == 'pictures') {
    $request = 'localhost:8085/pictures?caller=browser&bildtyp=' . $_GET["bildtyp"]
        . '&jahr=' . $_GET["jahr"] . '&ortid=' . $_GET["ortid"] . '&landid=' . $_GET["landid"] . '&nummer_von=' . $_GET["nummer_von"]
        . '&nummer_bis=' . $_GET["nummer_bis"] . '&suchtext=' . rawurlencode($_GET["suchtext"]) . '&wertung_min=' . $_GET["wertung_min"]
        . '&countperpage=' . $_GET["countperpage"] . '&runtype=' . $_GET["runtype"];

    // echo $request;
    $response = \Httpful\Request::get($request)->send();
} else if ($type == 'running') {
    $response = \Httpful\Request::get('localhost:8085/running?caller=browser&source=' . $_GET["source"]
        . '&id=' . $_GET["id"] . '&roomid=' . $_GET["roomid"])->send();
} else {
    echo 'Unknown type';
}

print_r($response->body);
// echo 'OK';
?>
