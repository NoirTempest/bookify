<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessUnit extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get the users for the business unit.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
