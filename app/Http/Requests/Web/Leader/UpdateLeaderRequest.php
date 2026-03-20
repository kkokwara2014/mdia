<?php

namespace App\Http\Requests\Web\Leader;

use App\Http\Requests\Web\Leader\Concerns\NormalizesLeaderSocialLinks;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeaderRequest extends FormRequest
{
    use NormalizesLeaderSocialLinks;

    public function authorize(): bool
    {
        return true;
    }

    protected function baseRules(): array
    {
        return [
            'role_uuid' => [
                'required',
                'string',
                Rule::exists('roles', 'uuid')->where(
                    fn ($q) => $q->whereNotIn('name', Role::EXCLUDED_FROM_LEADER_POSITION_SELECT)
                ),
            ],
            'order' => ['nullable', 'integer', 'min:0'],
            'user_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:2048'],
        ];
    }
}
