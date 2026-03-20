<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class SubmitPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $minYear = (int) (User::whereNotNull('registration_year')->where('registration_year', '!=', '')->min('registration_year') ?: 1900);
        $maxYear = (int) date('Y') + 1;

        return [
            'payment_type_uuid' => ['required', 'string', 'exists:payment_types,uuid'],
            'year' => ['required', 'integer', "min:{$minYear}", "max:{$maxYear}"],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'evidence_files' => ['nullable', 'array'],
            'evidence_files.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'mimetypes:image/jpeg,image/png,image/webp,application/pdf', 'max:2048'],
        ];
    }
}
