<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetFile extends Model
{
    protected $fillable = [
        'asset_detail_id',
        'file_attachments', // ✅ Fixed typo
    ];

    public function assetDetail(): BelongsTo
    {
        return $this->belongsTo(AssetDetail::class, 'asset_detail_id');
    }
}
