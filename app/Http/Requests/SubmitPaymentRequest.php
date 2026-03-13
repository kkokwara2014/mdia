<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_type_uuid' => ['required', 'string', 'exists:payment_types,uuid'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . date('Y')],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'evidence_files' => ['required', 'array', 'min:1'],
            'evidence_files.*' => ['required', 'string'],
        ];
    }
}
