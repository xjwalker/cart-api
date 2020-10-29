<?php

namespace App\Http\Controllers;

use App\Http\Gateway\ProductsGateway;
use App\Http\Requests\AddProductToCartRequest;
use App\Http\Requests\GetCartDetailsRequest;
use App\Http\Requests\RemoveProductFromCartRequest;
use App\Repositories\CartRepository;
use App\UseCases\AddProduct;
use App\UseCases\GetDetails;
use App\UseCases\RemoveProduct;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /** @var AddProduct */
    private $addProduct;
    /** @var CartRepository */
    private $cartRepository;
    /**@var ProductsGateway */
    private $productGateway;
    /** @var RemoveProduct */
    private $removeProduct;
    /** @var GetDetails */
    private $getDetails;

    /**
     * CartController constructor.
     * @param AddProduct $addProduct
     * @param CartRepository $cartRepository
     * @param GetDetails $getDetails
     * @param ProductsGateway $productGateway
     * @param RemoveProduct $removeProduct
     */
    public function __construct(
        AddProduct $addProduct,
        CartRepository $cartRepository,
        GetDetails $getDetails,
        ProductsGateway $productGateway,
        RemoveProduct $removeProduct
    ) {
        $this->addProduct = $addProduct;
        $this->cartRepository = $cartRepository;
        $this->getDetails = $getDetails;
        $this->productGateway = $productGateway;
        $this->removeProduct = $removeProduct;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCarts()
    {
        return response()->json(['data' => $this->cartRepository->getCarts()]);
    }

    /**
     * @param AddProductToCartRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addProduct(AddProductToCartRequest $request)
    {
        $cart = $request->get('cart');
        $products = $request->get('products');
        $gatewayProducts = $request->get('gatewayProducts');

        return response()->json(['data' => $this->addProduct->add($cart, $products, $gatewayProducts)]);
    }

    /**
     * @param RemoveProductFromCartRequest $request
     * @return \Illuminate\Http\Response
     */
    public function removeProduct(RemoveProductFromCartRequest $request)
    {
        $this->removeProduct->remove($request->get('cart'), $request->get('products'));

        return response('', 204);
    }

    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCartDetails(GetCartDetailsRequest $request)
    {
        return response()->json(['data' => $this->getDetails->get($request->getCart())]);
    }
}
