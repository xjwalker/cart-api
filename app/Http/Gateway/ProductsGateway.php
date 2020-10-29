<?php

namespace App\Http\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class ProductsGateway
{
    private const METHOD_GET = 'GET';

    /**
     * @var string
     */
    private $url;

    /**
     * @var Client
     */
    private $client;

    /**
     * ProductsGateway constructor.
     */
    public function __construct()
    {
        $this->url = config('services.product_service.url');
        $this->client = new Client(['base_uri' => $this->url]);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $queryString
     * @param array $body
     * @return array|mixed
     */
    private function makeCall(string $method, string $uri, array $queryString = [], array $body = [])
    {
        $options = [RequestOptions::HEADERS => []];

        if ($body) {
            $options[RequestOptions::FORM_PARAMS] = $body;
        }

        if ($queryString) {
            $options[RequestOptions::QUERY] = $queryString;
        }

        try {
            /** @var ResponseInterface $r */
            $r = $this->client->$method($uri, $options);
        } catch (ClientException $exception) {
            return [
                'error' => $exception->getMessage(),
            ];
        }

        return json_decode($r->getBody()->getContents(), true);
    }

    public function getProducts($products)
    {
        return $this->makeCall(self::METHOD_GET, '/product/list', $products);
    }

}
