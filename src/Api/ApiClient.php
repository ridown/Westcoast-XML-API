<?php


namespace Ridown\Westcoast\Api;

use Ridown\Westcoast\Exceptions\WestcoastResponseCouldNotBeParsed;
use GuzzleHttp\Client;

class ApiClient
{
    /** @var Client  */
    private $client;

    /** @var string */
    private $bearer;

    /** @var string */
    private $server;

    public function __construct(Client $client, string $server = null, string $bearer = null)
    {
        $this->client = $client;
        $this->server = $server;
        $this->bearer = $bearer;
    }

    public function get(array $parameters = [])
    {
        return $this->parse(function() use($url, $parameters){
            return $this->client->get($this->server.$url, [
                'form_params' => $parameters,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => $this->bearer
                ]
            ]);
        });
    }

    public function post(array $parameters = [])
    {
        return $this->parse(function() use($url, $parameters){
            return $this->client->post($this->server.$url, [
                'form_params' => $parameters,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json',
                    'Authorization' => $this->bearer ?? ''
                ]
            ]);
        });
    }

    public function post_int(array $parameters = []): int
    {
        return $this->parse(function() use($url, $parameters){
            return $this->client->post($this->server.$url, [
                'form_params' => $parameters,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json',
                    'Authorization' => $this->bearer ?? ''
                ]
            ]);
        });
    }
    
    private function parse(callable $callback)
    {
        $response = call_user_func($callback);

        $json = json_decode((string) $response->getBody(), true);

        if(json_last_error() !== JSON_ERROR_NONE){
            throw new WestcoastResponseCouldNotBeParsed((string) $response->getBody());
        }

        return $json;
    }

}
