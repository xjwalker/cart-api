<?php

use App\Models\Cart;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\CartTestUtils;
use Tests\TestCase;
use Tests\TestUtils;

class AddProductToCartTest extends TestCase
{
    use DatabaseTransactions;
    use CartTestUtils;
    use TestUtils;

    public function testAddProductToCart()
    {
        $products = [100, 101, 102];

        $this->setProductGatewayGetProducts($products);
        $this->createProducts();

        /** @var Cart $c */
        $c = Cart::factory()->create();

        // create mock
        $d = [
            'cart_id' => $c->id,
            'products' => [
                [
                    'id' => 100,
                    'amount' => 4,
                ],
                [
                    'id' => 101,
                    'amount' => 10,
                ],
                [
                    'id' => 102,
                    'amount' => 1,
                ]
            ],
        ];

        $r = $this->postJson('/api/cart/add-product', $d);
        $r->assertStatus(200);
    }

    public function testAddProductToANoneExistentCart()
    {
        $d = [
            'cart_id' => 100,
            'products' => ['hi'],
        ];

        $r = $this->postJson('/api/cart/add-product', $d);
        $r->assertStatus(422);
        $json = $r->json();
        $this->assertEquals('INVALID_EXISTS', $json['error']['errors']['cart_id']['code']);
    }

    public function testAddProductToCartNoneExistentProduct()
    {
        /** @var Cart $c */
        $c = Cart::factory()->create();

        $products = [100, 104];
        $this->setProductGatewayGetSingleProduct($products);

        $d = [
            'cart_id' => $c->id,
            'products' => [
                [
                    'id' => 100,
                    'amount' => 4,
                ],
                [
                    'id' => 104,
                    'amount' => 1,
                ]
            ],
        ];

        $r = $this->postJson('/api/cart/add-product', $d);
        $r->assertStatus(422);
        $json = $r->json();
        $this->assertEquals('INVALID_PRODUCT_EXISTENCE', $json['error']['errors']['products']['code']);
    }

    public function testAddMoreProductsThanAllowed()
    {
        /** @var Cart $c */
        $c = Cart::factory()->create();


        $d = [
            'cart_id' => $c->id,
            'products' => [
                [
                    'id' => 100,
                    'amount' => 4,
                ],
                [
                    'id' => 101,
                    'amount' => 1,
                ],
                [
                    'id' => 102,
                    'amount' => 1,
                ],
                [
                    'id' => 104,
                    'amount' => 1,
                ]
            ],
        ];

        $r = $this->postJson('/api/cart/add-product', $d);
        $r->assertStatus(422);
        $json = $r->json();
        $this->assertEquals('INVALID_AMOUNT_OF_PRODUCTS_PER_CART', $json['error']['errors']['products']['code']);
    }

    public function testAddMoreItemsPerProductThanAllowed()
    {
        /** @var Cart $c */
        $c = Cart::factory()->create();

        $d = [
            'cart_id' => $c->id,
            'products' => [
                [
                    'id' => 100,
                    'amount' => 4,
                ],
                [
                    'id' => 101,
                    'amount' => 1,
                ],
                [
                    'id' => 102,
                    'amount' => 16,
                ],
            ],
        ];

        $r = $this->postJson('/api/cart/add-product', $d);
        $r->assertStatus(422);
        $json = $r->json();
        $this->assertEquals('INVALID_AMOUNT_OF_ITEMS_PER_CART', $json['error']['errors']['products']['code']);
    }
}
