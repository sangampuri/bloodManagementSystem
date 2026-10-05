<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodInventory extends Model
{
    protected $fillable = [
        'blood_group', 'batch_code', 'units',
        'collected_on', 'expiry_date', 'added_by',
    ];

    protected function casts(): array
    {
        return [
            'collected_on' => 'date',
            'expiry_date' => 'date',
        ];
    }

    // Only batches that are not expired. Used for "available units".
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->whereDate('expiry_date', '>=', today());
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}