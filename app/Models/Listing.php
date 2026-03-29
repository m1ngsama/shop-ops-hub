<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'channel_id',
        'external_sku',
        'status',
        'price',
        'ad_daily_budget',
        'review_count',
        'conversion_rate',
        'performance_score',
        'last_synced_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'ad_daily_budget' => 'decimal:2',
            'conversion_rate' => 'decimal:2',
            'performance_score' => 'decimal:2',
            'last_synced_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
