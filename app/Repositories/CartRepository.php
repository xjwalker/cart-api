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
     * @param $cartId
     * @param $productId
     * @return bool
     */
    public function addProductToCart($cartId, $productId)
    {
        return DB::table('cart_products')->insert([
            'cart_id' => $cartId,
            'product_id' => $productId,
        ]);
    }

    public function getProductFromCart($cartId, $productIds)
    {
        return $this->getStatement($cartId, $productIds)->get();
    }

    /**
     * @param $cartId
     * @param $productIds
     * @param null $amount
     * @return int
     */
    public function removeProductsFromCart($cartId, $productIds, $amount = null)
    {
        return $this->getStatement($cartId, $productIds, $amount)->delete();
    }

    /**
     * @param $cartId
     * @param $productIds
     * @param $limit
     * @return \Illuminate\Database\Query\Builder
     */
    private function getStatement($cartId, $productIds, $limit = null)
    {
        $query = DB::table('cart_products')->where('cart_id', $cartId);
        if (is_int($productIds)) {
            $query->where('product_id', $productIds);
        } else {
            $query->whereIn('product_id', $productIds);
        }

        if ($limit) {
            $query->limit($limit);
        }
        return $query;
    }

}
