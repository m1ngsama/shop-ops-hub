<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_code',
        'batch_code',
        'quantity_on_hand',
        'quantity_reserved',
        'quantity_inbound',
        'inbound_eta',
    ];

    protected function casts(): array
    {
        return [
            'inbound_eta' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
