<?php

namespace boystark;

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


class RabbitMQService
{
    public static $service;

    public function sendMsg()
    {
        try {
            $connection = new AMQPStreamConnection('192.168.9.84', 5672, 'user', 'qwer');
            $channel = $connection->channel();
            $channel->queue_declare('hello', false, false, false, false);
            $msg = new AMQPMessage('Hello World King!');
            $channel->basic_publish($msg, '', 'hello');
            echo " [x] Sent 'Hello World!'\n";
            $channel->close();

            $connection->close();
        } catch (\Exception $e) {
            echo "sendMsg error:" . $e->getMessage();
        }
    }

    public function receiveMsg()
    {
        try {
            $connection = new AMQPStreamConnection('192.168.9.84', 5672, 'user', 'qwer');
            $channel = $connection->channel();
            $channel->queue_declare('hello', false, false, false, false);
            echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
            $callback = function ($msg) {
                echo " [x] Received ", $msg->body, "\n";
            };
            $channel->basic_consume('hello', '', false, true, false, false, $callback);
            while (count($channel->callbacks)) {
                $channel->wait();
            }

            $channel->close();

            $connection->close();
        } catch (\Exception $e) {
            echo "receiveMsg error:" . $e->getMessage();
        }
    }


    /**
     * 获取实例
     * @return RabbitMQService
     */
    public static function getInstance(): RabbitMQService
    {
        if (empty(self::$service)) {
            self::$service = new self();
        }
        return self::$service;
    }

}