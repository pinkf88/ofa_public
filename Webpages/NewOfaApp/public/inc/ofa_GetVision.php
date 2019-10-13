<?php
// http://phphttpclient.com/
// sudo apt-get install php7.0-curl (sudo apt-get install php-curl)
// php.ini

include('./httpful.phar');

$serieid = 0 + $_GET["serieid"];
$log = 0 + $_GET["log"];

$response = \Httpful\Request::get('localhost:8080/vision?serieid=' . $serieid . '&' . 'log=' . $log)->send();

print_r($response->body);
?>
