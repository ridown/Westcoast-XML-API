<?php

namespace Ridown\Westcoast\Api;

use Spatie\ArrayToXml\ArrayToXml;

class PNA extends ApiClient
{
    const TYPE = 'PNA';
    
    /**
     * is only available when using synchronous HTTPs.
     * The PNA Request message allows customers to query price and availability for specific products.
     *
     * @param array $skus
     * @return mixed
     */
    public function Request(array $skus = [])
    {
        $method = self::TYPE . 'Request';
        
        $request = $this->getRequestArray();
        
        $PNAInformation = [];
        foreach($skus as $sku) {
            $PNAInformation[] = ['_attributes' => ['SKU' => $sku]];
        }
        $request['PNAInformation'] = $PNAInformation;
        
        $xml = ArrayToXml::convert($request, $method);
        
        return $this->post($method, $xml);
    }
    
    

}
