<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'channel_id',
        'external_order_no',
        'status',
        'quantity',
        'sale_price',
        'ad_spend',
        'channel_fee',
        'ordered_at',
    ];

    protected function casts(): array
    {
        return [
            'sale_price' => 'decimal:2',
            'ad_spend' => 'decimal:2',
            'channel_fee' => 'decimal:2',
            'ordered_at' => 'datetime',
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

    public function revenue(): float
    {
        return round((float) $this->sale_price * $this->quantity, 2);
    }

    public function grossProfit(): float
    {
        $unitCost = (float) ($this->product?->cost_price ?? 0);

        return round($this->revenue() - ($unitCost * $this->quantity) - (float) $this->ad_spend - (float) $this->channel_fee, 2);
    }
}
