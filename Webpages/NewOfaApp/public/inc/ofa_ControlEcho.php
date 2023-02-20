<?php
// http://phphttpclient.com/
// sudo apt-get install php7.0-curl (sudo apt-get install php-curl)
// php.ini

include('./httpful.phar');

// print_r($_GET);

$roomid = $_GET["roomid"];

if (isset($_GET["get_all"])) {
    $url = 'localhost:8085/echo?get_all';
    $response = \Httpful\Request::get($url)->send();
} else if (isset($_GET["get_volume"])) {
    $url = 'localhost:8085/echo?get_volume';
    $response = \Httpful\Request::get($url)->send();
} else if (isset($_GET["set_volume"])) {
    $url = 'localhost:8085/echo?set_volume=' . $_GET["set_volume"];
    $response = \Httpful\Request::get($url)->send();
} else if (isset($_GET["get_treble"])) {
    $url = 'localhost:8085/echo?get_treble';
    $response = \Httpful\Request::get($url)->send();
} else if (isset($_GET["set_treble"])) {
    $url = 'localhost:8085/echo?set_treble=' . $_GET["set_treble"];
    $response = \Httpful\Request::get($url)->send();
} else if (isset($_GET["get_midrange"])) {
    $url = 'localhost:8085/echo?get_midrange';
    $response = \Httpful\Request::get($url)->send();
} else if (isset($_GET["set_midrange"])) {
    $url = 'localhost:8085/echo?set_midrange=' . $_GET["set_midrange"];
    $response = \Httpful\Request::get($url)->send();
} else if (isset($_GET["get_bass"])) {
    $url = 'localhost:8085/echo?get_bass';
    $response = \Httpful\Request::get($url)->send();
} else if (isset($_GET["set_bass"])) {
    $url = 'localhost:8085/echo?set_bass=' . $_GET["set_bass"];
    $response = \Httpful\Request::get($url)->send();
}

print_r($response->body);
// echo 'OK';
?>
