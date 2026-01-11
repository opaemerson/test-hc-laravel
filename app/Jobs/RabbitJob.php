<?php

namespace App\Jobs;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

abstract class RabbitJob
{
    protected function connect(): AMQPStreamConnection
    {
        return new AMQPStreamConnection(
            env('RABBITMQ_HOST'),
            env('RABBITMQ_PORT'),
            env('RABBITMQ_USER'),
            env('RABBITMQ_PASSWORD')
        );
    }

    protected function createChannel(AMQPStreamConnection $connection)
    {
        return $connection->channel();
    }

    protected function declareQueue($channel, $queue): void
    {
        $channel->queue_declare($queue, false, true, false, false);
    }

    protected function sendMessage($channel, $queue, $payload): void
    {
        $msg = new AMQPMessage(
            json_encode($payload),
            ['content_type' => 'application/json', 'delivery_mode' => 2]
        );
        $channel->basic_publish($msg, '', $queue);
    }

    protected function publish($queue, $payload): void
    {
        $connection = $this->connect();
        $channel = $this->createChannel($connection);
        $this->declareQueue($channel, $queue);
        $this->sendMessage($channel, $queue, $payload);
        $channel->close();
        $connection->close();
    }

    abstract public function handle(): void;
}
