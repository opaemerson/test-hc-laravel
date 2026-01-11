<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use App\Services\ApiResponseService;

class UserController
{
    public function __construct(
        private UserService $userService
    ) {}

    public function index()
    {
        return ApiResponseService::success(
            'Requisição realizada com sucesso',
            $this->userService->getAll()
        );
    }

    public function store(CreateUserRequest $request)
    {
        return ApiResponseService::success(
            'Usuário criado com sucesso',
            $this->userService->create($request->validated())
        );
    }

    public function show(int $id)
    {
        return ApiResponseService::success(
            'Requisição realizada com sucesso',
            $this->userService->getById($id)
        );
    }

    public function update(UpdateUserRequest $request, int $id)
    {
        return ApiResponseService::success(
            'Usuário atualizado com sucesso',
            $this->userService->update($id, $request->validated())
        );
    }

    public function destroy(int $id)
    {
        return ApiResponseService::success(
            'Usuário deletado com sucesso',
            $this->userService->delete($id)
        );
    }
}
