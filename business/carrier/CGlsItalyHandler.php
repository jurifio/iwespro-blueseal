<?php

namespace bamboo\business\carrier;

use bamboo\domain\entities\CShipment;

class CGlsItalyHandler extends ACarrierHandler {

    protected $config;

    private function generateEnvelope()
    {
        return new \SoapClient("https://weblabeling.gls-italy.com/IlsWebService.asmx?wsdl");
    }

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

    public function closeAndPrintPendentShipping($from = 0, $to = 'now')
    {
        // TODO: Implement closeAndPrintPendentShipping() method.
    }

    public function getBarcode($shipping)
    {
        // TODO: Implement getBarcode() method.
    }

    public function printParcelLabel(CShipment $shipment)
    {
        // TODO: Implement printParcelLabel() method.
    }


}