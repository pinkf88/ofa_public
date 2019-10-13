<?php
function ofa_db_connect($server, $user, $password, $database)
{
    $link = mysqli_connect($server, $user, $password, $database);

    if (mysqli_connect_errno())
    {
        printf("Connect failed: %s\n", mysqli_connect_error());
        return null;
    }

    // printf("1. Current character set: %s\n", mysqli_character_set_name($link));

    if (!mysqli_set_charset($link, "utf8"))
    {
        printf("Error loading character set utf8: %s\n", mysqli_error($link));
    }
    else
    {
        // printf("2. Current character set: %s\n", mysqli_character_set_name($link));
    }

    return $link;
}
?>
