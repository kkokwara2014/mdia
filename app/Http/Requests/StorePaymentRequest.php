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
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'evidence_files' => ['nullable', 'array'],
            'evidence_files.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf', 'mimetypes:image/jpeg,image/png,image/webp,application/pdf', 'max:2048'],
        ];
    }
}
