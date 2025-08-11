<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookedGuest extends Model
{
    protected $fillable = ['booking_id', 'email'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
