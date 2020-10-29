<?php

namespace App\UseCases;

use App\Repositories\CartRepository;

class RemoveProduct
{
    /**
     * @var CartRepository
     */
    private $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param $cart
     * @param $productsToDelete
     */
    public function remove($cart, $productsToDelete)
    {
        $ids = $productsToDelete->pluck('id')->toArray();
        $productsInCart = $this->cartRepository->getProductsByCartAndProductId($cart->id, $ids);

        $recordsToDelete = [];
        $productsToDelete->each(function ($product) use ($productsInCart, &$recordsToDelete) {
            $countOfProductInCart = $productsInCart->where('product_id', $product['id'])->count();
            // if we get a higher number than what we have, should we remove all?
            // or throw an exception? :shrug:
            if ($product['amount'] > $countOfProductInCart) {
                $recordsToDelete[$product['id']] = 10; // todo; extract this value.
            } else {
                $recordsToDelete[$product['id']] = $product['amount'];
            }
        });

        if (!empty($recordsToDelete)) {
            foreach ($recordsToDelete as $i => $amount) {
                $this->cartRepository->removeProductsFromCart($cart->id, $i, $recordsToDelete[$i]);
            }
        }
    }
}
