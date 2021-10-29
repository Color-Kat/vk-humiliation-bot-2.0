<?php
//
//use VK\Client\VKApiClient;

use app\lib\Log;

session_start();

define('IS_DEV', true);

$start = microtime(true);

require 'app/env.php';
require 'app/lib/dev.php';
require 'vendor/autoload.php';
require 'app/bootstrap.php';

$time = microtime(true) - $start;
Log::info("time: $time");

// last time is ~0.16298913955688


////$vk = new VKApiClient();
//
//function get_env_var($var)
//{
//    $config = json_decode(file_get_contents('./config.json'), true);
//
//    return $config[$var];
//}
//
//
//$data = json_decode(file_get_contents('php://input'));
//
//if (!$data) echo 'nioh';
//
//if (
//    $data->secret !== get_env_var('VK_SECRET_TOKEN') &&
//    $data->type !== 'confirmation'
//) echo 'nioh';
//
//switch ($data->type) {
//    case 'confirmation':
//        echo get_env_var('VK_CONFIRMATION_CODE');
//        break;
//
//    case 'message_new':
//        // create response array
//        $request_params = [
//            'user_id' => $data->object->message->from_id,
//            'message' => 'лошара',
//            'access_token' => get_env_var('VK_TOKEN'),
//            'v' => '5.81'
//        ];
//
//        // send message
//        file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));
//
//        echo 'ok';
//        break;
//
//    default:
//        echo 'nioh';
//        break;
//}
//
//
