<?php

namespace bamboo\business\carrier;

use bamboo\domain\entities\CShipment;

abstract class ACarrierHandler {

    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;

    }

    /**
     * Add a delivery Shipment, returns the Shipment
     * @param $source
     * @param $dest
     * @param $date
     * @param $notes
     * @return CShipment
     */
    public abstract function addDelivery($source,$dest,$date,$notes);

    /**
     * Close the day shipping printing the "borderò"
     * @param int $from
     * @param string $to
     * @return mixed
     */
    public abstract function closeAndPrintPendentShipping($from = 0,$to = 'now');

    /**
     * @param $shipping
     * @return mixed
     */
    public abstract function getBarcode($shipping);

    /**
     * @param CShipment $shipment
     * @return mixed
     */
    public abstract function printParcelLabel(CShipment $shipment);
}