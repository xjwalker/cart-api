<?php

namespace App\UseCases;

use App\Http\Gateway\ProductsGateway;
use App\Repositories\CartRepository;

class GetDetails
{
    /** @var CartRepository */
    private $cartRepository;
    /** @var ProductsGateway */
    private $productsGateway;

    /**
     * GetDetails constructor.
     * @param CartRepository $cartRepository
     * @param ProductsGateway $productsGateway
     */
    public function __construct(CartRepository $cartRepository, ProductsGateway $productsGateway)
    {
        $this->cartRepository = $cartRepository;
        $this->productsGateway = $productsGateway;
    }

    /**
     * @param $cart
     * @return array[]
     */
    public function get($cart)
    {
        $productsInCart = $this->cartRepository->getProductsFromCart($cart->id);

        $ids = array_unique($productsInCart->pluck('product_id')->toArray(), SORT_NUMERIC);
        $productsDetails = Collect($this->productsGateway->getProducts(array_values($ids)));

        list($products, $total) = $this->formatProductsDetails($productsInCart, $productsDetails);
        return [
            'cart' => [
                'id' => $cart->id,
                'total' => $total,
                'products' => $products,
            ]
        ];
    }

    /**
     * @param $productsInCart
     * @param $productDetails
     * @return array
     */
    private function formatProductsDetails($productsInCart, $productDetails)
    {
        $totalCost = 0;
        $result = $productDetails->map(function ($productDetail) use ($productsInCart, &$totalCost) {
            $productsInHand = $productsInCart->where('product_id', $productDetail['id']);
            $amount = $productsInHand->count();
            $totalCost += $productDetail['price'] * $amount;

            return [
                'id' => $productDetail['id'],
                'title' => $productDetail['title'],
                'amount' => $amount,
                'price' => $productDetail['price'],
                'added_to_cart_at' => $productsInHand->first()->created_at,
            ];
        });

        return [$result, $totalCost];
    }
}
