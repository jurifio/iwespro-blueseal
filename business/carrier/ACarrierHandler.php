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
     * @return CShipment
     */
    public abstract function addDelivery(CShipment $shipment);


    /**
     * @param CShipment $shipment
     * @return mixed
     */
    public abstract function cancelDelivery(CShipment $shipment);

    /**
     * @param $shippings
     * @return mixed
     */
    public abstract function closePendentShipping($shippings);

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
     * @return string
     */
    public abstract function printParcelLabel(CShipment $shipment);
}