<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Seeders\CommerceOpsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_page_displays_seeded_products(): void
    {
        $this->seed(CommerceOpsSeeder::class);

        $response = $this->get('/catalog');

        $response->assertOk();
        $response->assertSee('商品目录');
        $response->assertSee('修护发膜');
        $response->assertSee('过滤商品');
    }

    public function test_guest_can_add_product_to_selection_plan(): void
    {
        $this->seed(CommerceOpsSeeder::class);

        $product = Product::query()->where('sku', 'CARE-1001')->firstOrFail();

        $response = $this->post("/selection-plan/items/{$product->id}", [
            'quantity' => 2,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('storefront.selection_plan', [
            $product->id => 2,
        ]);

        $this->get('/selection-plan')
            ->assertOk()
            ->assertSee('意向清单')
            ->assertSee($product->name);
    }

    public function test_storefront_home_highlights_comparison_and_curated_collections(): void
    {
        $this->seed(CommerceOpsSeeder::class);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('场景组合');
        $response->assertSee('商品比较');
        $response->assertSee('轻健身与出行组合');
    }
}
