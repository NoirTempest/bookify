<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalLog extends Model
{
    protected $fillable = [
        'booking_id',
        'approver_id',
        'status',
        'remarks',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Get the booking that owns the approval log.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the approver that owns the approval log.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(Approver::class);
    }
}
