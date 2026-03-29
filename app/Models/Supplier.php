<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country_code',
        'lead_time_days',
        'contact_email',
        'quality_score',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
