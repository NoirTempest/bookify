<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the vehicle driver assignments for the driver.
     */
    public function vehicleDriverAssignments(): HasMany
    {
        return $this->hasMany(VehicleDriverAssignment::class);
    }
}
