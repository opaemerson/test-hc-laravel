<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiAuthRequest;
use App\Services\AuthService;

class AuthApiController
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function authenticate(ApiAuthRequest $request)
    {
        return $this->authService->authenticate(
            $request->login,
            $request->password
        );
    }
}
