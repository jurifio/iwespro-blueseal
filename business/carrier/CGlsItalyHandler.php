<?php

namespace bamboo\business\carrier;

use bamboo\domain\entities\CShipment;

class CGlsItalyHandler extends ACarrierHandler {

    protected $config;

    /**
     * @param $source
     * @param $dest
     * @param $date
     * @param $notes
     * @return CShipment
     */
    public function addDelivery($source,$dest,$date,$notes) {
        $asd = new CShipment();
        return $asd;
    }


}