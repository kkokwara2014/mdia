<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $paymentType = $this->route('paymentType');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('payment_types', 'name')->ignore($paymentType->id)],
            'amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
