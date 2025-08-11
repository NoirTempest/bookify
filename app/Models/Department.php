<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'branch_id', // make sure this foreign key exists in the table
    ];

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }
}
