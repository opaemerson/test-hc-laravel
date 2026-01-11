<?php

namespace App\DTO;

use Illuminate\Support\Facades\Hash;

class CreateUserDTO
{
    public string $name;
    public string $email;
    public string $password;

    public function __construct(array $data)
    {
        $this->name     = strtolower($data['name']);
        $this->email    = strtolower($data['email']);
        $this->password = Hash::make($data['password']);
    }

    public function toArray(): array
    {
        return [
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
        ];
    }
}
