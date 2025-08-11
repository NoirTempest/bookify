<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetDetail extends Model
{
    protected $fillable = [
        'asset_type_id',
        'asset_name',
        'location',
        'brand',
        'model',
        'color',
        'plate_number',
        'number_of_seats',
    ];

    protected $casts = [
        'number_of_seats' => 'integer',
    ];

    public function assetType(): BelongsTo
    {
        return $this->belongsTo(AssetType::class);
    }

    // ✅ Renamed to "files" for simplicity and to match Livewire view
    public function files(): HasMany
    {
        return $this->hasMany(AssetFile::class, 'asset_detail_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function vehicleDriverAssignments(): HasMany
    {
        return $this->hasMany(VehicleDriverAssignment::class);
    }
}
