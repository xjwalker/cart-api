<?php

namespace App\Http\Requests;

use App\Exceptions\ValidationRuleException;
use App\Http\Gateway\ProductsGateway;
use App\Repositories\CartRepository;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AddProductToCartRequest extends FormRequest
{

    private $cartRepository;
    /**
     * @var ProductsGateway
     */
    private $productGateway;

    public function __construct(CartRepository $cartRepository, ProductsGateway $productGateway)
    {
        parent::__construct();
        $this->cartRepository = $cartRepository;
        $this->productGateway = $productGateway;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    public function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->count()) {
                return;
            }
            $data = $validator->getData();

            // Make sure cart exists
            $cart = $this->cartRepository->getById($data['cart_id']);
            if (is_null($cart)) {
                throw new ValidationRuleException('cart_id', 'exists');
            }

            // make sure all the products exist.
            $productsCollection = Collect($data['products']);
            $p = $productsCollection->pluck('id')->toArray();

            // make sure that the amount of diff. products is not higher than 3.
            if ($productsCollection->count() > 3) {
                throw new ValidationRuleException('products', 'cart_product_limit');
            }

            // make sure he doesn't buy more than 10 times the same product.
            if ($productsCollection->where('amount', '>', 10)->first()) {
                throw new ValidationRuleException('products', 'cart_product_amount_limit');
            }

            // if the amount is 0, ignore the product.
            $gatewayProducts = Collect($this->productGateway->getProducts($p));
            if (count($p) > $gatewayProducts->count()) {
                throw new ValidationRuleException('products', 'products_does_not_match');
            }

            $this->request->add([
                'cart' => $cart,
                'products' => $productsCollection,
                'gatewayProducts' => $gatewayProducts,
            ]);
        });

        return $validator;
    }
}
