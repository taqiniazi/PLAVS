<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OwnerRequest extends Model
{
    protected $fillable = [
        'user_id',
        'library_name',
        'library_city',
        'library_country',
        'library_address',
        'library_phone',
        'status',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
