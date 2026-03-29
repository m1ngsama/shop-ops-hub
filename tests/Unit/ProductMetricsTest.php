<?php

namespace Tests\Unit;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductMetricsTest extends TestCase
{
    public function test_margin_rate_uses_target_price_cost_and_fulfillment_fee(): void
    {
        $product = new Product([
            'target_price' => 20.00,
            'cost_price' => 7.50,
            'fulfillment_fee' => 4.00,
        ]);

        $this->assertSame(42.5, $product->marginRate());
    }
}
