<?php

namespace App\Services;

use App\Models\User;
use App\DTO\CreateUserDTO;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use App\Services\RedisService;
use App\Jobs\PopulateLogEmails;

class UserService
{
    public function __construct(
        private RedisService $redisService
    ) {}


    public function getAll(): Collection
    {
        return User::all();
    }

    public function getById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function create(array $validatedData): User
    {
        $data = (new CreateUserDTO($validatedData))->toArray();
        $user = User::create($data);

        $this->cacheUser($user);
        $this->dispatchLogEmailJob($user);

        return $user;
    }

    public function update(int $id, array $validatedData): User
    {
        $user = User::findOrFail($id);

        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($validatedData);
        
        $this->cacheUser($user);

        return $user;
    }

    public function delete(int $id): User
    {
        $user = User::findOrFail($id);

        $user->delete();

        $this->redisService->forget("usuario:{$id}");

        return $user;
    }

    private function cacheUser(User $user): void
    {
        $this->redisService->put(
            key: "usuario:{$user->id}",
            value: $user,
            ttl: 300
        );
    }

    private function dispatchLogEmailJob(User $user): void
    {
        $job = new PopulateLogEmails([
            'message' => 'Bem-vindo ao sistema!',
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $job->handle();
    }
}
