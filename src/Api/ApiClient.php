<?php


namespace Ridown\Westcoast\Api;

use DOMDocument;
use GuzzleHttp\Client as GuzzleClient;;

use GuzzleHttp\Exception\RequestException;
use Ridown\Westcoast\Exceptions\WestcoastResponseCouldNotBeParsed;
use SoapClient;
use Illuminate\Support\Facades\Log;

class ApiClient
{
    /** @var Client  */
    private $client;

    /** @var array */
    private $config;
    
    /** @var int */
    private $debug;
    
    const BASE_URI = 'https://xmlportal.westcoast.co.uk/';
    protected $base_uri = '{$company_name}/inbound.php';

    public function __construct(GuzzleClient $client, array $config = [])
    {
        $this->client = $client;
        $this->config = $config;
    
        $this->debug  = data_get($this->config, 'debug');
    
        $company_name = data_get($this->config, 'auth_username');
        if(empty($company_name)) {
            dd(__METHOD__, 'Empty Company Name ', $company_name, $this->config);
        }
        $this->base_uri = self::BASE_URI . "{$company_name}/inbound.php";
    }
    
    
    
    public function debug($xml){
        $dom = new DOMDocument();

// Initial block (must before load xml string)
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
// End initial block
    
        $dom->loadXML($xml);
        $out = $dom->saveXML();
    
        return print_r($out, true);
    }

    
    public function get($method, $xml)
    {
    
        $auth_username  = data_get($this->config, 'auth_username');
        $auth_password  = data_get($this->config, 'auth_password');
        $base_url       = $this->base_uri;
        
        return $this->parse(function() use($xml,  $method, $auth_username, $auth_password, $base_url) {
            if($this->debug > 0) {
                Log::info(sprintf('[%s] Westcoast API GET : (%s) %s',
                    __METHOD__,
                    $base_url,
                    $xml
                ));
            }
            
            return $this->client->get($base_url, [
                'headers' => [
                    'Content-Type' => 'text/xml'
                ],
                'body' => trim($xml),
                'auth' => [
                    $auth_username,
                    $auth_password
                ],
            ]);
        });
    }

    public function post($method, $xml)
    {
    
        $auth_username  = data_get($this->config, 'auth_username');
        $auth_password  = data_get($this->config, 'auth_password');
        $base_url       = $this->base_uri;
        
        
        return $this->parse(function() use($xml,  $method, $auth_username, $auth_password, $base_url) {
    
            if($this->debug > 0) {
                Log::info(sprintf('[%s] Westcoast API POST : (%s) %s',
                    __METHOD__,
                    $base_url,
                    $xml
                ));
            }
    
            
            try {
                return $this->client->post($base_url, [
                    'body'    => trim($xml),
                    'headers' => [
                        'Content-Type' => 'text/xml'
                    ],
                    'auth'    => [
                        $auth_username,
                        $auth_password
                    ],
                ]);
            } catch(RequestException $e) {
                dd(__METHOD__, $e->getCode(), $e->getMessage(), $e, $xml);
            }
        });
        
        
    }

//    public function post_int(array $parameters = []): int
//    {
//        return $this->parse(function() use($url, $parameters){
//            return $this->client->post($this->server.$url, [
//                'form_params' => $parameters,
//                'headers' => [
//                    'Content-Type' => 'application/x-www-form-urlencoded',
//                    'Accept' => 'application/json',
//                    'Authorization' => $this->bearer ?? ''
//                ]
//            ]);
//        });
//    }
    
    private function parse(callable $callback)
    {
        try {
            $response = call_user_func($callback);
        } catch (\Exception $e) {
            Log::info(sprintf('[%s] Error from server: %s - %s',
                __METHOD__,
                $e->getCode(),
                $e->getMessage()
            ));
        }
        $code   = $response->getStatusCode();   // 200
        $reason = $response->getReasonPhrase(); // OK
        
        $json   = $this->xml_to_array((string) $response->getBody());
    
        if($this->debug > 1) {
            Log::info(sprintf('[%s] Westcoast API Response :  %s',
                __METHOD__,
                print_r($json, true)
            ));
        }
    
    
        if(json_last_error() !== JSON_ERROR_NONE){
            throw new WestcoastResponseCouldNotBeParsed((string) $response->getBody());
        }

        return $json;
    }
    
    public function xml_to_array(string $xml, bool $toArray=true) {
        $obj= simplexml_load_string($xml, null, LIBXML_NOCDATA);
        return json_decode(json_encode($obj), $toArray);
    }

    public function getRequestObject() {
        
        $requestObj = (object)new \stdClass();
        $requestObj->Version = '1.0';
        $requestObj->TransactionHeader = (object)new \stdClass();
        $requestObj->TransactionHeader->SenderID   = data_get($this->config, 'company');
        $requestObj->TransactionHeader->LoginID    = data_get($this->config, 'loginId');
        $requestObj->TransactionHeader->Password   = data_get($this->config, 'password');
        $requestObj->TransactionHeader->Company    = data_get($this->config, 'company');

        return $requestObj;
    }
    
    public function getRequestArray() {
        return [
            'Version' => '1.0',
            'TransactionHeader' => [
                'SenderID'   => data_get($this->config, 'company'),
                'LoginID'    => data_get($this->config, 'loginId'),
                'Password'   => data_get($this->config, 'password'),
                'Company'    => data_get($this->config, 'company'),
            ]
        ];
    }
    
}
