<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

function dump($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    // exit;
}

function dd($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    exit;
}
