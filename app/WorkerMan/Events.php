<?php

namespace App\WorkerMan;


use GatewayClient\Gateway;

class Events
{
    public static function onWorkerStart($businessWorker)
    {

    }

    public static function onConnect($client_id)
    {
        Gateway::sendToClient($client_id, json_encode([
            'type' => 'init',
            'client_id' => $client_id
        ]));
    }

    public static function onWebsocketConnect($client_id, $data)
    {

    }

    public static function onMessage($client_id, $message)
    {

    }

    public static function onClose($client_id)
    {

    }
}
