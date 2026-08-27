<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerVerification extends Model
{
    protected $fillable = [
        'seller_profile_id',
        'status',
        'document_type',
        'document_path',
        'notes',
        'rejection_reason',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function sellerProfile(): BelongsTo
    {
        return $this->belongsTo(SellerProfile::class);
    }
}
