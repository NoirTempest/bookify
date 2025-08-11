<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetType extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get the asset details for the asset type.
     */
    public function assetDetails(): HasMany
    {
        return $this->hasMany(AssetDetail::class);
    }

    /**
     * Get the bookings for the asset type.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the approvers for the asset type.
     */
    public function approvers(): HasMany
    {
        return $this->hasMany(Approver::class);
    }
}
