<?php

namespace Ridown\Westcoast\Api;


use Spatie\ArrayToXml\ArrayToXml;


class Order extends ApiClient
{
    const TYPE = 'Order';
    
    
    /**
     * The Order Request message allows customers to place orders with Westcoast.
     * The Order Request specifies typical order information such as products, quantities and shipping address.
     *
     * @param array $data
     * @return mixed
     */
    public function Request(array $data = [])
    {
        $method = self::TYPE . 'Request';
        
        $request = $this->getRequestArray();
    
        $request['Order'] = $this->buildOrder($data);
        
        $xml = ArrayToXml::convert($request, $method);
        
        return $this->post($method, $xml);
    }
    
    
    /**
     * This message is only available when using synchronous HTTPs.
     * This message is sent to Westcoast to request the status of an order.
     *
     * @param null $BranchOrderNumber
     * @param      $CustomerPO
     * @return mixed
     */
    public function DetailRequest($BranchOrderNumber = null, $CustomerPO = null)
    {
        $method = self::TYPE . 'DetailRequest';
        
        $request = $this->getRequestArray();
        
        $request['OrderHeaderInfo'] = [
            'BranchOrderNumber' => $BranchOrderNumber,
            'CustomerPO' => $CustomerPO
        ];
        
        $xml = ArrayToXml::convert($request, $method);
        
        return $this->post($method, $xml);
    }
    
    
    
    
    
    
    
    
    
    
    public function buildOrder(array $data) {
    
        $OrderHeaderInformation = [
              'ShipToSuffix' => null,
              'AddressingInformation' => [
                  'ShipTo' => [
                      'Address' => [
                          'ShipToAttention' => data_get($data, 'OrderHeaderInformation.AddressingInformation.Address.ShipToAttention'),
                          'ShipToAddress1' => data_get($data, 'OrderHeaderInformation.AddressingInformation.Address.ShipToAddress1'),
                          'ShipToAddress2' => data_get($data, 'OrderHeaderInformation.AddressingInformation.Address.ShipToAddress2'),
                          'ShipToAddress3' => data_get($data, 'OrderHeaderInformation.AddressingInformation.Address.ShipToAddress3'),
                          'ShipToCity' => data_get($data, 'OrderHeaderInformation.AddressingInformation.Address.ShipToCity'),
                          'ShipToProvince' => data_get($data, 'OrderHeaderInformation.AddressingInformation.Address.ShipToProvince'),
                          'ShipToPostalCode' => data_get($data, 'OrderHeaderInformation.AddressingInformation.Address.ShipToPostalCode'),
                          'ShipToCountry' => data_get($data, 'OrderHeaderInformation.AddressingInformation.Address.ShipToCountry'),
                      ],
                      'ContactDetails' => [
                          'TelephoneNumber' => data_get($data, 'OrderHeaderInformation.AddressingInformation.ContactDetails.TelephoneNumber'),
                          'FaxNumber' => data_get($data, 'OrderHeaderInformation.AddressingInformation.ContactDetails.FaxNumber'),
                          'EMail' => data_get($data, 'OrderHeaderInformation.AddressingInformation.ContactDetails.EMail'),
                          'NotifyEMail' => data_get($data, 'OrderHeaderInformation.AddressingInformation.ContactDetails.NotifyEMail'),
                          'ContactName' => data_get($data, 'OrderHeaderInformation.AddressingInformation.ContactDetails.ContactName'),
                      ],
                  ],
                  'CustomerPO' => data_get($data, 'OrderHeaderInformation.AddressingInformation.CustomerPO'),
                  'EndUserPO' => data_get($data, 'OrderHeaderInformation.AddressingInformation.EndUserPO'),
              ],
              'ProcessingOptions' => [
                  'CarrierCode' => data_get($data, 'OrderHeaderInformation.ProcessingOptions.CarrierCode'),
                  'CarrierCodeValue' => data_get($data, 'OrderHeaderInformation.ProcessingOptions.CarrierCodeValue'),
                  'OrderDueDate' => data_get($data, 'OrderHeaderInformation.ProcessingOptions.OrderDueDate'),
                  'ShipmentOptions' => [
                      'BackOrderFlag' => data_get($data, 'OrderHeaderInformation.ProcessingOptions.ShipmentOptions.BackOrderFlag', 'Y'),
                      'SplitShipmentFlag' => data_get($data, 'OrderHeaderInformation.ProcessingOptions.ShipmentOptions.SplitShipmentFlag', 'N'),
                  ]
              ]
        ];
    
        $OrderLineInformation = [];
        
        foreach (data_get($data, 'OrderLineInformation.ProductLine', []) as $ProductLine) {
            $OrderLineInformation[] = [
                'ProductLine' => [
                    'SKU' => data_get($ProductLine, 'SKU'),
                    'AlternateSKU' => data_get($ProductLine, 'AlternateSKU'),
                    'Quantity' => data_get($ProductLine, 'Quantity', 1),
                    'FixedPrice' => data_get($ProductLine, 'FixedPrice', '0.00'),
                    'CustomerLineNumber' => data_get($ProductLine, 'CustomerLineNumber'),
                    'LineComment' => data_get($ProductLine, 'LineComment'),
                ]
            ];
        }
    
        $CommentText = data_get($data, 'CommentLine.CommentText', '');
        
        $order = [
            'OrderHeaderInformation' => $OrderHeaderInformation,
            'OrderLineInformation'   => $OrderLineInformation,
            'CommentLine'            => ['CommentText' => $CommentText],
        ];
        
        
        
        return $order;
    }
    
    

}
