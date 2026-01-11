<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiAuthRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'login' => 'required|string',
            'password' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'login.required'     => 'O campo login é obrigatório.',
            'login.string'       => 'O login deve ser um texto.',

            'password.required' => 'A senha é obrigatória.',
            'password.string'   => 'A senha deve ser um texto.',
        ];
    }
}

