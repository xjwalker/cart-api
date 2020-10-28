<?php

namespace App\Http\Controllers;

use App\Http\Gateway\ProductsGateway;
use App\Http\Requests\AddProductToCartRequest;
use App\Repositories\CartRepository;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /** @var CartRepository */
    private $cartRepository;

    /**
     * @var ProductsGateway
     */
    private $productGateway;

    /**
     * CartController constructor.
     * @param CartRepository $cartRepository
     * @param ProductsGateway $productGateway
     */
    public function __construct(CartRepository $cartRepository, ProductsGateway $productGateway)
    {
        $this->cartRepository = $cartRepository;
        $this->productGateway = $productGateway;
    }

    public function createCart()
    {
        return response()->json(['data' => $this->cartRepository->create()]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteCart(Request $request)
    {
        $cart = $request->get('cart');
        return response()->json(['data' => $this->cartRepository->delete($cart)]);
    }

    public function getCarts()
    {
        return response()->json(['data' => $this->cartRepository->getCarts()]);
    }

    /**
     * @param AddProductToCartRequest $request
     * @return array
     */
    public function addProduct(AddProductToCartRequest $request)
    {
        $cart = $request->get('cart');
        $products = $request->get('products');
        $gatewayProducts = Collect($request->get('gatewayProducts'));

        $result = $products->map(function ($product) use ($cart, $gatewayProducts) {
            $currentProduct = $gatewayProducts->where('id', $product['id'])->first();
            for ($i = 0; $i < $product['amount']; $i++) {
                $this->cartRepository->addProductToCart($cart, $product);
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
