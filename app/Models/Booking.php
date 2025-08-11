<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_type_id',
        'asset_detail_id',
        'user_id',
        'purpose',
        'no_of_seats',
        'destination',
        'scheduled_date',
        'time_from',
        'time_to',
        'notes',
        'status',
        'first_approver_name',
        'first_approved_at',
        'second_approver_name',
        'second_approved_at',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'time_from' => 'datetime:H:i',
        'time_to' => 'datetime:H:i',
    ];

    protected $appends = ['formatted_schedule'];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     */

    // public function user()
    // {
    //     return $this->belongsTo(\App\Models\User::class);
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function assetType()
    // {
    //     return $this->belongsTo(\App\Models\AssetType::class);
    // }

    public function assetType()
    {
        return $this->belongsTo(AssetType::class);
    }

    // public function assetDetail()
    // {
    //     return $this->belongsTo(\App\Models\AssetDetail::class);
    // }

    public function assetDetail()
    {
        return $this->belongsTo(AssetDetail::class);
    }

    public function vehicleDriverAssignments()
    {
        return $this->hasMany(\App\Models\VehicleDriverAssignment::class, 'booking_id');
    }

    public function bookedGuests()
    {
        return $this->hasMany(BookedGuest::class);
    }

    public function guests()
    {
        return $this->hasMany(BookedGuest::class);
    }
    public function vehicleAssignments()
    {
        return $this->hasMany(VehicleDriverAssignment::class);
    }


    public function approvalLogs()
    {
        return $this->hasMany(\App\Models\ApprovalLog::class);
    }

    public function approver1()
    {
        return $this->belongsTo(User::class, 'approver_1_id');
    }

    public function approver2()
    {
        return $this->belongsTo(User::class, 'approver_2_id');
    }

    // ✅ Vehicle assignment (1:1)
    public function vehicleAssignment()
    {
        return $this->hasOne(\App\Models\VehicleDriverAssignment::class, 'booking_id');
    }

    // ✅ Shortcut to access assigned driver (via vehicleAssignment)
    public function driver()
    {
        return $this->hasOneThrough(
            \App\Models\Driver::class,
            \App\Models\VehicleDriverAssignment::class,
            'booking_id', // Foreign key on vehicle_driver_assignments
            'id',         // Foreign key on drivers
            'id',         // Local key on bookings
            'driver_id'   // Local key on vehicle_driver_assignments
        );
    }

    /*
     |--------------------------------------------------------------------------
     | Accessors
     |--------------------------------------------------------------------------
     */

    public function getFormattedScheduleAttribute()
    {
        $date = $this->scheduled_date
            ? $this->scheduled_date->format('l, F j, Y')
            : 'N/A';

        $from = $this->time_from
            ? Carbon::parse($this->time_from)->format('g:i A')
            : 'N/A';

        $to = $this->time_to
            ? Carbon::parse($this->time_to)->format('g:i A')
            : 'N/A';

        return "$date — $from to $to";
    }
}
