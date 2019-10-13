<?php
$session = $_GET["session"];
$key = $_GET["key"];
$value = '';

session_start();

if (isset ($_SESSION[$key]))
{
    $value = $_SESSION[$key];
}

// echo $session . ' ' . $key . ' ' . $value;

echo '{';
echo '  "info": "' . $session . ' ' . $key . ' ' . $value . '",';
echo '  "value": "' . $value . '"';
echo '}';
?>
