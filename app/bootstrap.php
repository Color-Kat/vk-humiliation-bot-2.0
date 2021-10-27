<?php

\app\lib\Log::init();

function bot_env($var)
{
    $bot_config = json_decode(file_get_contents(APP . '/config/bot_config.json'), true);

    return $bot_config[$var];
}

$router = new app\core\Router;
$router->run();