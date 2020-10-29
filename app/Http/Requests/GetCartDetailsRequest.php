<?php

namespace App\Http\Requests;

use App\Repositories\CartRepository;
use Illuminate\Foundation\Http\FormRequest;

class GetCartDetailsRequest extends FormRequest
{

    /** @var CartRepository */
    private $cartRepository;

    /**
     * GetCartDetailsRequest constructor.
     * @param CartRepository $cartRepository
     */
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
            'cart_id' => 'bail|required|filled'
        ];
    }

    public function getCart()
    {
        return $this->cartRepository->getById(request()->get('cart_id'));
    }
}
