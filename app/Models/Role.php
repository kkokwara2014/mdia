<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Role extends Model
{
    /** Account-type roles; not leadership titles for the org chart select. */
    public const EXCLUDED_FROM_LEADER_POSITION_SELECT = ['Admin', 'Super Admin', 'Member'];

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function scopeForLeaderPositionSelect(Builder $query): Builder
    {
        return $query->whereNotIn('name', self::EXCLUDED_FROM_LEADER_POSITION_SELECT)->orderBy('name');
    }
}
