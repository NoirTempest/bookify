<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleDriverAssignment extends Model
{
    protected $fillable = [
        'booking_id',
        'driver_id',
        'asset_detail_id',
        'assigned_date',
        'assigned_by',
        'odometer_start',
        'odometer_end',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'odometer_start' => 'decimal:2',
        'odometer_end' => 'decimal:2',
    ];

    /**
     * Get the booking that owns the vehicle driver assignment.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the driver that owns the vehicle driver assignment.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get the asset detail that owns the vehicle driver assignment.
     */
    public function assetDetail(): BelongsTo
    {
        return $this->belongsTo(AssetDetail::class);
    }

    /**
     * Get the user who assigned the vehicle driver assignment.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
