<?php

use App\Models\Cart;
use App\Repositories\CartRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\CartTestUtils;
use Tests\TestCase;

class RemoveProductFromCartTest extends TestCase
{
    use CartTestUtils;

    /** @var CartRepository */
    private $cartRepository;

    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartRepository = $this->app->make(CartRepository::class);
    }

    public function testRemoveProductFromCart()
    {
        /** @var Cart $c */
        $c = Cart::factory()->create();
        $this->addProductsToCart($c->id);

        $d = [
            'cart_id' => $c->id,
            'products' => [
                [
                    'id' => 100,
                    'amount' => 2,
                ],
                [
                    'id' => 102,
                    'amount' => 1,
                ]
            ],
        ];
        $r = $this->deleteJson('/api/cart/remove-product', $d);
        $r->assertStatus(204);

        // this tests deletes
        // 2 records for product.id 100
        // 1 record for product 102 (all records)
        $this->assertDatabaseHas('cart_products', [
            'cart_id' => $c->id,
            'product_id' => 100,
        ]);
        $this->assertDatabaseHas('cart_products', [

            'cart_id' => $c->id,
            'product_id' => 101,
        ]);
        $this->assertDatabaseMissing('cart_products', [
            'cart_id' => $c->id,
            'product_id' => 102,
        ]);
    }

    public function testRemoveProductFromCartProductsDontExist()
    {
        /** @var Cart $c */
        $c = Cart::factory()->create();

        $d = [
            'cart_id' => $c->id,
            'products' => [
                [
                    'id' => 100,
                    'amount' => 2,
                ],
                [
                    'id' => 102,
                    'amount' => 1,
                ]
            ],
        ];
        $r = $this->deleteJson('/api/cart/remove-product', $d);
        $r->assertStatus(422);
        $json = $r->json();
        $this->assertEquals('INVALID_PRODUCTS_DONT_MATCH_CART', $json['error']['errors']['products']['code']);

    }

    public function testRemoveProductFromCartDontExist()
    {
        $d = [
            'cart_id' => 10,
            'products' => [
                [
                    'id' => 100,
                    'amount' => 2,
                ],
                [
                    'id' => 102,
                    'amount' => 1,
                ]
            ],
        ];
        $r = $this->deleteJson('/api/cart/remove-product', $d);
        $r->assertStatus(422);
        $json = $r->json();
        $this->assertEquals('INVALID_EXISTS', $json['error']['errors']['cart_id']['code']);

    }

    public function testRemoveProductFromCartProductsDontBelongToCart()
    {
        /** @var Cart $realCart */
        $realCart = Cart::factory()->create();
        $this->addProductsToCart($realCart->id);

        /** @var Cart $impostorCart */
        $impostorCart = Cart::factory()->create();

        $d = [
            'cart_id' => $impostorCart->id,
            'products' => [
                [
                    'id' => 100,
                    'amount' => 2,
                ],
                [
                    'id' => 102,
                    'amount' => 1,
                ]
            ],
        ];
        $r = $this->deleteJson('/api/cart/remove-product', $d);
        $r->assertStatus(422);
        $json = $r->json();
        $this->assertEquals('INVALID_PRODUCTS_DONT_MATCH_CART', $json['error']['errors']['products']['code']);

    }

    public function testRemoveProductFromCartMoreProductsThanWhatCartHolds()
    {
        /** @var Cart $c */
        $c = Cart::factory()->create();
        $this->addProductsToCart($c->id);

        $d = [
            'cart_id' => $c->id,
            'products' => [
                [
                    'id' => 100,
                    'amount' => 10,
                ],
                [
                    'id' => 102,
                    'amount' => 12,
                ]
            ],
        ];
        $r = $this->deleteJson('/api/cart/remove-product', $d);
        $r->assertStatus(204);

        // this tests deletes all products [100, 102]
        $this->assertDatabaseMissing('cart_products', [
            'cart_id' => $c->id,
            'product_id' => 100,
        ]);
        $this->assertDatabaseHas('cart_products', [

            'cart_id' => $c->id,
            'product_id' => 101,
        ]);
        $this->assertDatabaseMissing('cart_products', [
            'cart_id' => $c->id,
            'product_id' => 102,
        ]);
    }

}
