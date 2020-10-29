<?php

namespace Tests;

use App\Repositories\CartRepository;
use Illuminate\Support\Facades\DB;

trait CartTestUtils
{
    private function createProducts()
    {
        return \Illuminate\Support\Facades\DB::table('products')->insert([
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
    }

    private function addProductsToCart($cartId)
    {
        $this->createProducts();
        $products = DB::table('products')->orderBy('id', 'asc')->get();

        /** @var CartRepository $cartRepository */
        $cartRepository = $this->cartRepository;

        // these records are completely related to the Stub we use in tests.
        $firstProduct = $products->pop();
        $cartRepository->addProductToCart($cartId, $firstProduct->id);

        $secondProduct = $products->pop();
        for ($i = 0; $i < 10; $i++) {
            $cartRepository->addProductToCart($cartId, $secondProduct->id);
        }

        $thirdProduct = $products->pop();
        for ($i = 0; $i < 4; $i++) {
            $cartRepository->addProductToCart($cartId, $thirdProduct->id);
        }
    }
}
