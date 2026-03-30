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
        $response->assertSee('找到适合你的商品');
        $response->assertSee('修护发膜');
        $response->assertSee('筛选');
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
            ->assertSee('购物袋')
            ->assertSee($product->name);
    }

    public function test_storefront_home_highlights_comparison_and_curated_collections(): void
    {
        $this->seed(CommerceOpsSeeder::class);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('本周推荐');
        $response->assertSee('精选组合');
        $response->assertSee('轻运动与出行组合');
        $response->assertSee('按类目浏览');
    }
}
