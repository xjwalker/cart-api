<?php

namespace App\Http\Requests;

use App\Exceptions\ValidationRuleException;
use App\Repositories\CartRepository;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RemoveProductFromCartRequest extends FormRequest
{
    /** @var CartRepository */
    private $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        parent::__construct();
        $this->cartRepository = $cartRepository;
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
        return [
            'cart_id' => 'bail|required|filled',
            'products' => 'bail|required|filled',
        ];
    }

    public function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();
        $validator->after(function (Validator $validator) {
            $data = $validator->getData();

            $c = $this->cartRepository->getById($data['cart_id']);

            if (is_null($c)) {
                throw new ValidationRuleException('cart_id', 'exists');
            }

            // check that all products belong to cart
            $products = Collect($data['products']);

            if ($products->count() > 3) { // TODO; EXTRACT THIS VALUE
                throw new ValidationRuleException('products', 'cart_product_limit');
            }

            $ids = $products->pluck('id')->toArray();
            $productsInCart = $this->cartRepository->getProductFromCart($data['cart_id'], $ids)->count();
            if ($productsInCart < $products->count()) {
                throw new ValidationRuleException('products', 'cart_product_does_not_match');
            }


            $this->request->add(['cart' => $c, 'products' => $products]);
        });

        return $validator;
    }
}
