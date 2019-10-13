<?php
$session = $_GET["session"];
$key = $_GET["key"];
$value = $_GET["value"];

session_start();

$_SESSION[$key] = $value;

// echo $session . ' ' . $key . ' ' . $value;
?>
