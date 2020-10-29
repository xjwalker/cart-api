<?php

use App\Models\Cart;
use App\Repositories\CartRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\CartTestUtils;
use Tests\TestCase;
use Tests\TestUtils;

class GetCartDetailsTest extends TestCase
{
    use DatabaseTransactions;
    use CartTestUtils;
    use TestUtils;

    private $cartRepository;

    const JSON_STRUCTURE = [
        'data' => [
            'cart' => [
                'id',
                'total',
                'products' => [
                    '*' => [
                        'id',
                        'title',
                        'price',
                        'added_to_cart_at'
                    ],
                ],
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartRepository = $this->app->make(CartRepository::class);
    }

    public function testGetCartDetails()
    {
        /** @var Cart $c */
        $c = Cart::factory()->create();
        $products = [100, 101, 102];
        $this->setProductGatewayGetProducts($products);
        $this->addProductsToCart($c->id);

        $r = $this->getJson('/api/cart/details?cart_id=' . $c->id);
        $r->assertStatus(200);
        $r->assertJsonStructure(self::JSON_STRUCTURE);

        $json = $r->json();
        $this->assertEquals(757.49, $json['data']['cart']['total']);
        $this->assertEquals($c->id, $json['data']['cart']['id']);
        $this->assertCount(3, $json['data']['cart']['products']);
    }

    // todo; create test with empty cart.

    // todo; create test with non existing cart.

}
