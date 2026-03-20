<?php

namespace App\Http\Requests\Web\Leader\Concerns;

use App\Models\Leader;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

trait NormalizesLeaderSocialLinks
{
    protected function prepareForValidation(): void
    {
        $raw = $this->input('social_links');
        if ($raw === null || !is_array($raw)) {
            $this->merge(['social_links' => []]);

            return;
        }

        $allowed = array_keys(Leader::socialPlatformDefinitions());
        $cleaned = [];
        foreach ($raw as $row) {
            if (!is_array($row)) {
                continue;
            }
            $url = trim((string) ($row['url'] ?? ''));
            $platform = $row['platform'] ?? '';
            if ($url === '' && $platform === '') {
                continue;
            }
            if ($url === '' || !in_array($platform, $allowed, true)) {
                continue;
            }
            $cleaned[] = ['platform' => $platform, 'url' => $url];
        }

        $this->merge(['social_links' => $cleaned]);
    }

    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            'social_links' => ['nullable', 'array'],
            'social_links.*.platform' => ['required', Rule::in(array_keys(Leader::socialPlatformDefinitions()))],
            'social_links.*.url' => ['required', 'string', 'url', 'max:2048'],
        ]);
    }

    abstract protected function baseRules(): array;

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $links = $this->input('social_links', []);
            if (!is_array($links)) {
                return;
            }
            $seen = [];
            foreach ($links as $i => $row) {
                if (!is_array($row)) {
                    continue;
                }
                $p = $row['platform'] ?? null;
                if (!$p) {
                    continue;
                }
                if (isset($seen[$p])) {
                    $validator->errors()->add('social_links', 'Each social platform can only be added once.');

                    return;
                }
                $seen[$p] = true;
            }
        });
    }
}
