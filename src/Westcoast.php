<?php

namespace Ridown\Westcoast;

use Ridown\Westcoast\Api\PNA;

use GuzzleHttp\Client as GuzzleClient;

class Westcoast
{
    const BASE_URI = 'https://xmlportal.westcoast.co.uk/';
    protected $base_uri = '{$company}/inbound.php';
  
    /** @var GuzzleClient */
    protected $client;

    /** @var array */
    protected $config;

    /** @var string */
    protected $bearer;

    /** @var string */
    protected $server;

    /** @var string */
    protected $response;
  
    public function __construct(array $config, GuzzleClient $client = null)
    {
        
        /*
            $config = [
                'loginId' => env('WESTCOAST_LOGIN_ID'),
                'password' => env('WESTCOAST_PASSWORD'),
                'company' => env('WESTCOAST_COMPANY'),
                'timeout' => null
            ];
        */
        
        
        $this->client = $client ?: $this->makeClient();
        $this->config = $config;
        $company = data_get($this->config, 'company');
        $this->base_uri = self::BASE_URI . "{$company}/inbound.php";
    
        
        if(! $this->bearer){
            $this->refreshToken();
        }
    }
    
    public static function make(array $config, GuzzleClient $client = null): self
    {
        return new static ($config, $client);
    }
  
    private function makeClient(): GuzzleClient
    {
        return new GuzzleClient([
            'timeout' => $this->config['timeout'] ?? 30
        ]);
    }
  
    private function refreshToken() : void
    {
      /*
       * todo: set this as per Westcoast requiremtn
       */
        $parameters = [
            "ApplicationId" => $this->config['applicationId'],
            "ApplicationSecret" => $this->config['applicationSecret'],
            "Token" => $this->config['token']
        ];

        $response = (new Auth($this->client, self::BASE_URI.'/api/', null))
            ->AuthorizeByApplication($parameters);

        if(! ($response['Token'] ?? null)){
            throw new LinnworksAuthenticationException($response['message'] ?? '');
        }

        $this->bearer = $response['Token'];

        $this->server = $response['Server'] .'/api/';

        $this->response = $response;
    }

    public function response()
    {
        return $this->response;
    }

    public function PNA(): PNA
    {
        return new PNA($this->client, $this->server, $this->bearer);
    }
  
  
}
