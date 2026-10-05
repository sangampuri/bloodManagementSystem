<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodRequest extends Model
{
    // status, admin_note, reviewed_by, reviewed_at are NOT fillable.
    // User cannot set them from a form. Only our code sets them.
    protected $fillable = [
        'user_id', 'donor_id', 'blood_group', 'quantity',
        'hospital_name', 'hospital_address', 'contact_number',
        'urgency', 'reason', 'message',
    ];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}