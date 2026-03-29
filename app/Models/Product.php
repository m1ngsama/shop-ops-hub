<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'sku',
        'name',
        'category',
        'status',
        'marketplace_focus',
        'cost_price',
        'target_price',
        'fulfillment_fee',
        'safety_stock',
        'lead_time_days',
        'selling_points',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'target_price' => 'decimal:2',
            'fulfillment_fee' => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function inventoryBatches(): HasMany
    {
        return $this->hasMany(InventoryBatch::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function availableInventory(): int
    {
        if ($this->relationLoaded('inventoryBatches')) {
            return (int) $this->inventoryBatches->sum(function (InventoryBatch $batch): int {
                return $batch->quantity_on_hand - $batch->quantity_reserved + $batch->quantity_inbound;
            });
        }

        return (int) $this->inventoryBatches()
            ->selectRaw('COALESCE(SUM(quantity_on_hand - quantity_reserved + quantity_inbound), 0) as total')
            ->value('total');
    }

    public function marginRate(): float
    {
        $targetPrice = (float) $this->target_price;

        if ($targetPrice === 0.0) {
            return 0.0;
        }

        $margin = (($targetPrice - (float) $this->cost_price - (float) $this->fulfillment_fee) / $targetPrice) * 100;

        return round($margin, 1);
    }
}
