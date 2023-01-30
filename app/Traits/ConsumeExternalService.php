<?php

namespace App\Traits;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

trait ConsumeExternalService
{
    /**
     * Send Http requests and throws exception for all error responses
     *
     * @param  mixed $method
     * @param  mixed $endpoint
     * @param  mixed $formParams
     * @param  mixed $headers
     * @return JsonString
     */
    public function performRequest($method, $endpoint, $formParams = [], $headers = [])
    {
        $client = new Client([
            'base_uri' => $this->baseUri
        ]);

        if (isset($this->secret)) {
            $headers['Authorization'] = "Bearer $this->secret";
        }

        $response = $client->request($method, $endpoint, ['form_params' => $formParams, 'headers' => $headers]);
        // dd($response);

        return $response->getBody()->getContents();
    }

    /**
     * Sends Http requests and does not throw an exception for error responses except 500 level errors
     * 
     * @param  mixed $method
     * @param  mixed $endpoint
     * @param  mixed $formParams
     * @param  mixed $headers
     * @return JsonString
     */
    public function makeRequest($method, $endpoint, $formParams = [], $headers = [])
    {
        if (isset($this->secret)) {
            $headers['Authorization'] = "Bearer $this->secret";
        }

        $response = Http::withHeaders($headers)
            ->{strtolower($method)}(
                $this->baseUri . $endpoint,
                $formParams
            );

        // throw an exception for error level 500
        $response->throwIf($response->serverError());

        return $response->body();
    }

    public function performBasicRequest($method, $endpoint, $formParams = [], $headers = [])
    {
        $client = new Client([
            'base_uri' => $this->baseUri
        ]);

        if (isset($this->basicToken)) {
            $headers['Authorization'] = "Basic $this->basicToken";
        }

        $response = $client->request($method, $endpoint, ['form_params' => $formParams, 'headers' => $headers]);

        return $response->getBody()->getContents();
    }
}
