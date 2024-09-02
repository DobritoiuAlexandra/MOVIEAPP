<?php

namespace Src\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class MovieApiClient
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function buildUrl($movieType, $page): string
    {
        return "https://api.themoviedb.org/3/movie/$movieType?language=en-US&page=$page";
    }

    /**
     * @throws GuzzleException
     */
    public function getMovies($url, $api_key)
    {
        try {
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'Authorization' => $api_key,
                    'accept' => 'application/json',
                ],
            ]);

            $body = $response->getBody();
            $array_body = json_decode($body);

            return $array_body->results ?? [];
        } catch (RequestException $e) {
            return null; // Handle the error as needed
        }
    }
}
