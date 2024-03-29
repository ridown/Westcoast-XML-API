<?php

namespace Ridown\Westcoast;


use GuzzleHttp\Client as GuzzleClient;
use Ridown\Westcoast\Api\Dispatch;
use Ridown\Westcoast\Api\Order;
use Ridown\Westcoast\Api\PNA;

use SoapClient;

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
  
    public function __construct(array $config = null, GuzzleClient $client = null)
    {
        
        /*
            $config = [
                'loginId' => env('WESTCOAST_LOGIN_ID'),
                'password' => env('WESTCOAST_PASSWORD'),
                'company' => env('WESTCOAST_COMPANY'),
                'timeout' => null
            ];
        */
        if(!$config) {
            $config = config('westcoast');
        }
        
        
        
        $this->config = $config;
        $company_name = data_get($this->config, 'WESTCOAST_AUTH_USERNAME');
        $this->base_uri = self::BASE_URI . "{$company_name}/inbound.php";
    
        $this->client = $client ?: $this->makeClient();
    
//        if(! $this->bearer){
//            $this->refreshToken();
//        }
    }
    
    public static function make(array $config, GuzzleClient $client = null): self
    {
        return new static ($config, $client);
    }
    
    private function makeClient()
    {
        return $this->makeGuzzleClient();
    }
    
    
    private function makeGuzzleClient()
    {
        return new GuzzleClient([
            'timeout' => data_get($this->config, 'timeout', 30),
            'base_url' => $this->base_uri,
        ]);
    }
    
    /*
     * furture use..  the Westcoast API does not support proper SOAP
     * is more of a SOAP string structure
     */
    private function makeSoapClient()
    {
        $context = stream_context_create([
            'http' => [
                'protocol_version' => '1.1',
                'user_agent'       => 'RIDOWN Titan',
                //                'header'           => 'AgentID: '. config('vodafone.agentid'),
            ],
        ]);
    
        $options = [
            'location'       => $this->base_uri,
            'uri'            => 'urn:xmethods-delayed-quotes',
            'trace'          => 1,
            'exceptions'     => 0,
            'stream_context' => $context,
            'soap_version'   => SOAP_1_1,
            'cache_wsdl'     => WSDL_CACHE_NONE,
            'authentication' => SOAP_AUTHENTICATION_BASIC,
            'login'          => data_get($this->config, 'auth_username'),
            'password'       => data_get($this->config, 'auth_password'),
        ];
    
    
    
        return new SoapClient(null,$options);
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
        return new PNA($this->client, $this->config);
    }
    
    public function Order(): Order
    {
        return new Order($this->client, $this->config);
    }
    
    public function Dispatch(): Dispatch
    {
        return new Dispatch($this->client, $this->config);
    }
  
  
}
