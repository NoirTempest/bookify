<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\HasMany;

    class Approver extends Model
    {
        protected $fillable = [
            'asset_type_id',
            'user_id',
            'approver_level',
        ];

        protected $casts = [
            'approver_level' => 'integer',
        ];

        /**
         * Get the asset type that owns the approver.
         */
        public function assetType(): BelongsTo
        {
            return $this->belongsTo(AssetType::class);
        }

        

        /**
         * Get the user that owns the approver.
         */
        public function user(): BelongsTo
        {
            return $this->belongsTo(User::class);
        }

        /**
         * Get the approval logs for the approver.
         */
        public function approvalLogs(): HasMany
        {
            return $this->hasMany(ApprovalLog::class);
        }
    }
