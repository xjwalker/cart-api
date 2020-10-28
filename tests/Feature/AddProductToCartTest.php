<?php

use App\Http\Gateway\ProductsGateway;
use App\Models\Cart;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tests\TestUtils;

class AddProductToCartTest extends TestCase
{
    use DatabaseTransactions;
    use TestUtils;

    /**
     *
     */
    public function testAddProductToCart()
    {
        $products = [100, 101, 102];

        $jsonStub = $this->loadJson('tests/Stubs/GetProducts.json');
        $this->mock(ProductsGateway::class, function ($mock) use ($products, $jsonStub) {
            $mock->shouldReceive()->getProducts($products)->once()->andReturn($jsonStub);
        });

        DB::table('products')->insert([
            [
                'id' => 100,
                'title' => "Miss Frederique O'Keefe Jr.",
                'price' => 20.36,
            ],
            [
                'id' => 101,
                'title' => 'Wilma Eichmann I',
                'price' => 64.16,
            ],
            [
                'id' => 102,
                'title' => 'Vida Murray',
                'price' => 34.45,
            ],
        ]);

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
}
