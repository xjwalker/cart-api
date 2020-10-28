<?php

namespace App\Repositories;

use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class CartRepository
{
    /**
     * @return Cart
     */
    public function create(): Cart
    {
        $c = new Cart();
        $c->save();
        return $c;
    }

    /**
     * @param Cart $cart
     * @return Cart
     * @throws \Exception
     */
    public function delete(Cart $cart): Cart
    {
        $cart->delete();
        return $cart;
    }

    public function getCarts($limit = 100)
    {
        return Cart::query()->limit($limit)->get();
    }

    /**
     * @param int $id
     * @return Cart|null
     */
    public function getById(int $id): ?Cart
    {
        return Cart::find($id);
    }

    /**
     * @param $cart
     * @param $product
     * @return bool
     */
    public function addProductToCart($cart, $product)
    {
        return DB::table('cart_products')->insert([
            'cart_id' => $cart->id,
            'product_id' => $product['id'],
        ]);
    }
}
