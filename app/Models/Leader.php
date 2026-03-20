<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Leader extends Model
{
    protected $fillable = [
        'user_id',
        'uuid',
        'role_id',
        'image',
        'social_links',
        'is_published',
        'order',
    ];

    protected $hidden = [
        'id',
        'user_id',
        'role_id',
        'role',
    ];

    protected $appends = [
        'image_url',
        'position',
    ];

    protected $with = [
        'role',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'order' => 'integer',
            'social_links' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Leader $leader) {
            if (empty($leader->uuid)) {
                $leader->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    protected function position(): Attribute
    {
        return Attribute::get(fn () => $this->role?->name ?? '');
    }

    public function getImageUrl(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        if ($this->user && $this->user->user_image) {
            return asset('storage/' . $this->user->user_image);
        }

        return asset('assets/default-avatar.png');
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->getImageUrl());
    }

    public function getName(): string
    {
        return $this->user->name ?? 'Unknown';
    }

    public static function socialPlatformDefinitions(): array
    {
        return [
            'facebook' => [
                'label' => 'Facebook',
                'frontend_icon' => 'icofont-facebook',
                'admin_icon' => 'ti-brand-facebook',
            ],
            'instagram' => [
                'label' => 'Instagram',
                'frontend_icon' => 'icofont-instagram',
                'admin_icon' => 'ti-brand-instagram',
            ],
            'linkedin' => [
                'label' => 'LinkedIn',
                'frontend_icon' => 'icofont-linkedin',
                'admin_icon' => 'ti-brand-linkedin',
            ],
            'twitter' => [
                'label' => 'X (Twitter)',
                'frontend_icon' => 'icofont-twitter',
                'admin_icon' => 'ti-brand-x',
            ],
        ];
    }

    public function socialLinksForDisplay(): array
    {
        $defs = self::socialPlatformDefinitions();
        $raw = $this->social_links;
        if (!is_array($raw)) {
            return [];
        }

        $out = [];
        foreach ($raw as $item) {
            if (!is_array($item)) {
                continue;
            }
            $p = $item['platform'] ?? '';
            $u = isset($item['url']) ? trim((string) $item['url']) : '';
            if (!isset($defs[$p]) || $u === '' || !filter_var($u, FILTER_VALIDATE_URL)) {
                continue;
            }
            $out[] = [
                'platform' => $p,
                'url' => $u,
                'icon' => $defs[$p]['frontend_icon'],
            ];
        }

        return $out;
    }
}
