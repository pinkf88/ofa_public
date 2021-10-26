<?php
include('./httpful.phar');
// print_r($_GET);

$url = 'localhost:8085/ofapics?numbers=' . $_GET["numbers"];
$response = \Httpful\Request::get($url)->send();
print_r($response->body);
?>
