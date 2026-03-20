<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'user_image',
        'country_of_residence',
        'registration_year',
    ];

    protected $hidden = [
        'id',
        'password',
        'remember_token',
        'email_verified_at',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });

        static::created(function ($model) {
            $memberRole = Role::firstOrCreate(['name' => 'Member']);
            if (!$model->roles()->where('role_id', $memberRole->id)->exists()) {
                $model->roles()->attach($memberRole->id);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function getAvatarUrl(): string
    {
        if ($this->user_image) {
            return asset('storage/' . $this->user_image);
        }
        return asset('assets/user_avatar.png');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function leader(): HasOne
    {
        return $this->hasOne(Leader::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function verifiedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'verified_by');
    }

    public function hasPermission(string $permission): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', $permission);
            })
            ->exists();
    }

    public static function getPaymentYearRange(): array
    {
        $minYear = (int) (static::whereNotNull('registration_year')
            ->where('registration_year', '!=', '')
            ->min('registration_year') ?: 1900);
        $maxYear = (int) date('Y') + 1;

        return ['min' => $minYear, 'max' => $maxYear];
    }
}
