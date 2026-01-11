<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthService
{
    public function __construct(
        private RedisService $redisService
    ) {}

    public function authenticate(string $login, string $password): JsonResponse
    {
        if ($login !== config('api.login') || $password !== config('api.password')) {
            throw new UnauthorizedHttpException('', 'Credenciais inválidas');
        }

        $token = hash('sha256', Str::random(60) . time());

        $this->redisService->put(
            key: "auth:token:{$token}",
            value: true,
            ttl: 7200
        );

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 7200,
        ]);
    }
}
