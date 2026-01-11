<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumeRabbitQueue extends Command
{
    protected $signature = 'rabbit:consume';
    protected $description = 'Consumir fila PopulateLogEmailQueue';

    public function handle()
    {
        $connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', 'rabbitmq'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest')
        );

        $channel = $connection->channel();
        $channel->queue_declare('PopulateLogEmailQueue', false, true, false, false);

        $callback = function (AMQPMessage $msg) {
            $data = json_decode($msg->body, true);

            echo "Payload processado: " . json_encode($data) . PHP_EOL;
        };

        $channel->basic_consume(
            'PopulateLogEmailQueue',
            '',
            false,
            true,
            false,
            false,
            $callback
        );

        $this->info("📡 Aguardando mensagens...");

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
