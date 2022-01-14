<?php

namespace Ridown\Westcoast\Api;

use Spatie\ArrayToXml\ArrayToXml;

class Dispatch extends ApiClient
{
    const TYPE = 'Dispatch';
    
    public function Advice(array $skus = [])
    {
        $method = self::TYPE . 'AdviceRequest';
        $request = $this->getRequestArray();
        
        $xml = ArrayToXml::convert($request, $method);
        
        return $this->post($method, $xml);
    }
    
    

}
