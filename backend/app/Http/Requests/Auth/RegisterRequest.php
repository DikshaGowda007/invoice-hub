<?php

namespace App\Http\Requests\Auth;

use App\Constants\CommonConstant;
use App\Constants\HttpStatusConstant;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();
        throw new HttpResponseException(response()->json([
            'status' => CommonConstant::ERROR,
            'message' => $firstError,
        ], HttpStatusConstant::UNPROCESSABLE_ENTITY));
    }

    public function attributes(): array
    {
        return [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email Id',
            'password' => 'Password',
        ];
    }

    protected $fields = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];
}
