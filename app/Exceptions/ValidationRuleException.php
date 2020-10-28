<?php

namespace App\Exceptions;

use Exception;

class ValidationRuleException extends Exception
{
    protected $attribute;
    protected $message;
    protected $rule;

    /**
     * ValidationRuleException constructor.
     * @param string $attribute
     * @param string $rule
     * @param string|null $message
     */
    public function __construct(string $attribute, string $rule, string $message = null)
    {
        parent::__construct();
        $this->message = $message;
        $this->attribute = $attribute;
        $this->rule = $rule;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        return response()->json([
            'error' => [
                'type' => 'ValidationException',
                'status' => 422,
                'errors' => [
                    $this->attribute => [
                        'code' => config('errors.' . strtolower($this->rule), 'UNKNOWN'),
                        'message' => $this->getCustomMessage(),
                    ],
                ],
            ],
        ], 422);
    }

    private function getCustomMessage()
    {
        if (is_null($this->message)) {
            return trans('validation.' . strtolower($this->rule), ['attribute' => $this->attribute]);
        }
        return $this->message;
    }
}
