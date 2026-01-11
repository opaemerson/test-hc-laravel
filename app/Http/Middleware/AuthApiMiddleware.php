<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\ApiResponseService;

class AuthApiMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $authorization = $request->header('Authorization');

        if (! $authorization || ! str_starts_with($authorization, 'Bearer ')) {
            return ApiResponseService::error('Cabeçalho Authorization ausente ou inválido',[]);
        }

        $token = trim(substr($authorization, 7));
        $cacheKey = "auth:token:{$token}";

        if (! Cache::store('redis')->has($cacheKey)) {
            return ApiResponseService::error('Token inválido ou expirado', []);
        }

        return $next($request);
    }
}
