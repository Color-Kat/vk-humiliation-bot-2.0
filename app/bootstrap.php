<?php

\app\lib\Log::init();

function bot_env($var)
{
    $bot_config = json_decode(file_get_contents(APP . '/config/bot_config.json'), true);

    return $bot_config[$var];
}

function mb_ucfirst(string $string): string
{
    $firstChar = mb_substr($string, 0, 1);
    $then = mb_substr($string, 1, null);
    return mb_strtoupper($firstChar) . $then;
}

$router = new app\core\Router;
$router->run();