<?php

namespace App\Http\Requests\V1\BusinessProfile\Update;

use App\Constants\CommonConstant;
use App\Constants\HttpStatusConstant;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DetailsRequest extends FormRequest
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
            'company_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'tax_id' => ['nullable', 'string', 'max:50'],
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
            'company_name' => 'Company Name',
            'address' => 'Address',
            'phone' => 'Phone',
            'tax_id' => 'Tax ID',
        ];
    }

    protected $fields = [
        'company_name',
        'address',
        'phone',
        'tax_id',
    ];
}
