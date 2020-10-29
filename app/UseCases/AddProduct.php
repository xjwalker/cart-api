<?php


namespace App\UseCases;

use App\Http\Gateway\ProductsGateway;
use App\Repositories\CartRepository;

class AddProduct
{
    /**
     * @var ProductsGateway
     */
    private $productsGateway;
    /**
     * @var CartRepository
     */
    private $cartRepository;

    /**
     * AddProduct constructor.
     * @param CartRepository $cartRepository
     * @param ProductsGateway $productsGateway
     */
    public function __construct(CartRepository $cartRepository, ProductsGateway $productsGateway)
    {
        $this->productsGateway = $productsGateway;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param $cart
     * @param $products
     * @param $gatewayProducts
     * @return array
     */
    public function add($cart, $products, $gatewayProducts)
    {
        $result = $products->map(function ($product) use ($cart, $gatewayProducts) {
            $currentProduct = $gatewayProducts->where('id', $product['id'])->first();
            for ($i = 0; $i < $product['amount']; $i++) {
                $this->cartRepository->addProductToCart($cart->id, $product['id']);
            }

            return [
                'id' => $product['id'],
                'title' => $currentProduct['title'],
                'individual_price' => $currentProduct['price'],
                'amount' => $product['amount'] * $currentProduct['price'],
            ];
        });

        return [
            'cart_id' => $cart->id,
            'total' => $result->sum('amount'),
            'products' => $result,
        ];
    }
}
