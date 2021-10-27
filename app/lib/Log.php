<?php

namespace app\lib;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log
{
    private static Logger $logger;

    public static function init() {
        // create a log channel
        self::$logger = new Logger('test');
        self::$logger->pushHandler(new StreamHandler(ROOT . '/logs/.log', Logger::DEBUG));
    }

    public static function info($mess) {
        self::$logger->info($mess);
    }
}