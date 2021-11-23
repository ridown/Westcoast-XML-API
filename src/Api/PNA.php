<?php

namespace Ridown\Westcoast\Api;

class PNA extends ApiClient
{

    public function Request(array $skus = [])
    {
        $PNAInformation = [];
        foreach($skus as $sku) {
          $PNAInformation[] = $sku;
        }
        return $this->get([
            "PNAInformation" => $PNAInformation
        ]);
    }

}
