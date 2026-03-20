<?php

namespace App\Http\Requests\Web\Leader;

use App\Http\Requests\Web\Leader\Concerns\NormalizesLeaderSocialLinks;
use Illuminate\Foundation\Http\FormRequest;

class StoreLeaderRequest extends FormRequest
{
    use NormalizesLeaderSocialLinks;

    public function authorize(): bool
    {
        return true;
    }

    protected function baseRules(): array
    {
        return [
            'user_uuid' => ['required', 'string', 'exists:users,uuid'],
            'position' => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
            'user_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:2048'],
        ];
    }
}
