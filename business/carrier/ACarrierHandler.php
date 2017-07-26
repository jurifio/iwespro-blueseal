<?php

namespace bamboo\business\carrier;

use bamboo\domain\entities\CShipment;

abstract class ACarrierHandler {

    protected $config;

    public function __construct(array $config)
    {
        //$this->config = $config;
    }

    /**
     * Add a delivery Shipment, returns the Shipment
     * @param CShipment $shipment
     * @return mixed
     */
    public abstract function addDelivery(CShipment $shipment);

    /**
     * Close the day shipping
     * @param int $from
     * @param string $to
     * @return mixed
     */
    public abstract function closePendentShipping($from = 0,$to = 'now');

    /**
     * Close the confirmed day shipping
     * @return mixed
     */
    public abstract function printDayShipping();

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