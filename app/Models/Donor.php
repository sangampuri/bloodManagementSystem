<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donor extends Model
{
    protected $fillable = [
        'user_id', 'blood_group', 'gender', 'age',
        'phone', 'location', 'last_donation_date', 'is_available',
    ];

    protected function casts(): array
    {
        return [
            'last_donation_date' => 'date',
            'is_available' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bloodRequests(): HasMany
    {
        return $this->hasMany(BloodRequest::class);
    }
}