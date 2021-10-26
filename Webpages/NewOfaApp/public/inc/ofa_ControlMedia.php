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
            . '&runtype=' . $_GET["runtype"]
            . '&roomid=' . $_GET["roomid"]
            . '&personid=' . $_GET["personid"];

        $no_compilations = 0;

        if (isset($_GET["no_compilations"])) {
            $no_compilations = $_GET["no_compilations"];
        }

        $url .= '&no_compilations=' . $no_compilations;

        $response = \Httpful\Request::get($url)->send();
    } else if (isset($_GET["direction"])) {
        $response = \Httpful\Request::get('localhost:8085/audio?direction=' . $_GET["direction"] . '&roomid=' . $_GET["roomid"])->send();
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
    } else if (isset($_GET["albumid"]) && isset($_GET["update"])) {
        // Updating album info (with musicbrainz and discogs data)
        $response = \Httpful\Request::get('localhost:8085/audio?update&albumid=' . $_GET["albumid"])->send();
    } else if (isset($_GET["albumid"])) {
        // Getting album info
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
} else if ($type == 'flaglist') {
    if (isset($_GET["add"])) {
        $response = \Httpful\Request::get('localhost:8085/flaglist?add&albumid=' . $_GET["albumid"])->send();
    } else if (isset($_GET["remove"])) {
        $response = \Httpful\Request::get('localhost:8085/flaglist?remove&albumid=' . $_GET["albumid"])->send();
    } else if (isset($_GET["list"])) {
        $response = \Httpful\Request::get('localhost:8085/flaglist?list')->send();
    }
} else if ($type == 'starlist') {
    if (isset($_GET["add"])) {
        $response = \Httpful\Request::get('localhost:8085/starlist?add&albumid=' . $_GET["albumid"] . '&year=' . $_GET["year"])->send();
    } else if (isset($_GET["remove"])) {
        $response = \Httpful\Request::get('localhost:8085/starlist?remove&albumid=' . $_GET["albumid"] . '&year=' . $_GET["year"])->send();
    } else if (isset($_GET["list"])) {
        $response = \Httpful\Request::get('localhost:8085/starlist?list')->send();
    }
} else if ($type == 'videolist') {
    if (isset($_GET["list"])) {
        $response = \Httpful\Request::get('localhost:8085/videolist?list')->send();
    } else if (isset($_GET["play"])) {
        $response = \Httpful\Request::get('localhost:8085/videolist?play&url=' . $_GET["url"])->send();
    } else if (isset($_GET["seek"])) {
        $response = \Httpful\Request::get('localhost:8085/videolist?seek&url=' . $_GET["url"])->send();
    } else if (isset($_GET["stop"])) {
        $response = \Httpful\Request::get('localhost:8085/videolist?stop')->send();
    } else if (isset($_GET["pause"])) {
        $response = \Httpful\Request::get('localhost:8085/videolist?pause')->send();
    } else if (isset($_GET["info"])) {
        $response = \Httpful\Request::get('localhost:8085/videolist?info')->send();
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
} else if ($type == 'pictures') {
    $request = 'localhost:8085/pictures?caller=browser&bildtyp=' . $_GET["bildtyp"]
        . '&jahr=' . $_GET["jahr"] . '&ortid=' . $_GET["ortid"] . '&landid=' . $_GET["landid"] . '&nummer_von=' . $_GET["nummer_von"]
        . '&nummer_bis=' . $_GET["nummer_bis"] . '&suchtext=' . rawurlencode($_GET["suchtext"]) . '&wertung_min=' . $_GET["wertung_min"]
        . '&countperpage=' . $_GET["countperpage"] . '&runtype=' . $_GET["runtype"];

    // echo $request;
    $response = \Httpful\Request::get($request)->send();
} else if ($type == 'admin') {
    if (isset($_GET["restart_webmedia"])) {
        $response = \Httpful\Request::get('localhost:8085/admin?restart_webmedia')->send();
    } else if (isset($_GET["reconnect_kodi"])) {
        $response = \Httpful\Request::get('localhost:8085/admin?reconnect_kodi')->send();
    } else if (isset($_GET["restart_kodi"])) {
        $response = \Httpful\Request::get('localhost:8085/admin?restart_kodi')->send();
    } else if (isset($_GET["restart_echostudio"])) {
        $response = \Httpful\Request::get('localhost:8085/admin?restart_echostudio')->send();
    } else if (isset($_GET["restart_echoshow"])) {
        $response = \Httpful\Request::get('localhost:8085/admin?restart_echoshow')->send();
    } else if (isset($_GET["restart_webhome"])) {
        $response = \Httpful\Request::get('localhost:8085/admin?restart_webhome')->send();
    } else if (isset($_GET["audio_volumecheck"])) {
        $response = \Httpful\Request::get('localhost:8085/admin?audio_volumecheck')->send();
    } else if (isset($_GET["update_videolist"])) {
        $response = \Httpful\Request::get('localhost:8085/admin?update_videolist')->send();
    } else if (isset($_GET["update_playlists"])) {
        $response = \Httpful\Request::get('localhost:8085/admin?update_playlists')->send();
    } else if (isset($_GET["update_analytics"])) {
        $response = \Httpful\Request::get('localhost:8085/admin?update_analytics')->send();
    } else if (isset($_GET["update_databases_all"])) {
        $response = \Httpful\Request::get('localhost:8085/admin?update_databases&domain=all')->send();
    } else if (isset($_GET["update_databases_jr"])) {
        $response = \Httpful\Request::get('localhost:8085/admin?update_databases&domain=jr')->send();
    } else if (isset($_GET["update_databases_eib"])) {
        $response = \Httpful\Request::get('localhost:8085/admin?update_databases&domain=eib')->send();
    } else if (isset($_GET["restart_iobroker"])) {
        $response = \Httpful\Request::get('localhost:8085/admin?restart_iobroker')->send();
    } else if (isset($_GET["info_iobroker"])) {
        $response = \Httpful\Request::get('localhost:8085/admin?info_iobroker')->send();
    }
} else if ($type == 'running') {
    $response = \Httpful\Request::get('localhost:8085/running?caller=browser&source=' . $_GET["source"]
        . '&id=' . $_GET["id"] . '&roomid=' . $_GET["roomid"])->send();
} else {
    echo 'Unknown type';
}

print_r($response->body);
// echo 'OK';
?>
