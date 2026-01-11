<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class RedisService
{
    public function put(string $key, mixed $value, int $ttl): void
    {
        Cache::put($key, $value, now()->addSeconds($ttl));
    }

    public function get(string $key): mixed
    {
        return Cache::get($key);
    }

    public function forget(string $key): void
    {
        Cache::forget($key);
    }
}
