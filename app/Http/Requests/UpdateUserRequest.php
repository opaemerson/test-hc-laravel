<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:50',
            'email' => "sometimes|required|email|unique:users,email",
            'password' => 'sometimes|nullable|min:6',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 50 caracteres.',

            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O email informado não é válido.',
            'email.unique' => 'O email informado já está em uso.',
            
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'error'    => true,
                    'message'  => $validator->errors()->first(),
                    'response' => $validator->errors()->all(),
                ], 422)
            );
        }

        throw new HttpResponseException(
            redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
        );
    }

}
