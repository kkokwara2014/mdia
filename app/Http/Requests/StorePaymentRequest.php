<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_uuid' => ['required', 'string', 'exists:users,uuid'],
            'payment_type_uuid' => ['required', 'string', 'exists:payment_types,uuid'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . date('Y')],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'evidence_files' => ['nullable', 'array'],
            'evidence_files.*' => ['file', 'max:2048', 'mimes:jpeg,jpg,png,webp,pdf'],
        ];
    }
}
