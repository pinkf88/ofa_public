<?php
// http://phphttpclient.com/
// sudo apt-get install php7.0-curl (sudo apt-get install php-curl)
// php.ini

include('./httpful.phar');

// print_r($_GET);

$base_url = 'localhost:8084/home/';

if (isset($_GET['temperatures'])) {
    // echo "temperatures is set\n";
    $response = \Httpful\Request::get($base_url . '?temperatures')->send();
} else if (isset($_GET['day'])) {
    // echo "days is set\n";
    $response = \Httpful\Request::get($base_url . '?day=' . $_GET['day'])->send();
} else {
    echo 'Unknown type';
    return;
}

print_r($response->body);
?>
