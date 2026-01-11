<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ];
    }

    public function messages()
    {
        return [
            'name.required'     => 'O campo login é obrigatório.',
            'name.string'       => 'O login deve ser um texto.',
            'name.max' => 'O nome não pode ter mais de 50 caracteres.',

            'email.required'    => 'O campo e-mail é obrigatório.',
            'email.email'       => 'O e-mail deve ser um endereço de e-mail válido.',
            'email.unique'      => 'O e-mail já está em uso.',

            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
        ];
    }
}

