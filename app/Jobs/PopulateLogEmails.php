<?php

namespace App\Jobs;

class PopulateLogEmails extends RabbitJob
{
    private array $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle(): void
    {
        $this->publish('PopulateLogEmailQueue', $this->data);
    }
}

