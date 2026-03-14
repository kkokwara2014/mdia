<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PaymentEvidence extends Model
{
    protected $table = 'payment_evidences';

    protected $fillable = [
        'uuid',
        'payment_id',
        'file_path',
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

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
