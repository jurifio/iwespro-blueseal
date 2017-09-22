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

    /**
     * @param $province
     * @return int|string
     */
    protected function getProvinceCode($province)
    {
        $province = trim($province);
        if (strlen($province) == 2 && isset($this->getProvinceList()[strtoupper($province)])) return strtoupper($province);
        $lev = 30;
        $provinceCode = "";
        foreach ($this->getProvinceList() as $key => $val) {
            if($province == $val) return $key;
            $nLev = levenshtein($val, $province);
            if ($nLev < $lev) {
                $lev = $nLev;
                $provinceCode = $key;
            }
        }
        return $provinceCode;
    }

    /**
     * @return array
     */
    protected function getProvinceList()
    {
        if (!isset($this->provinces)) {
            $this->provinces = [];
            $provinces = \Monkey::app()->repoFactory->create('Province')->findAll();
            foreach ($provinces as $province) {
                $this->provinces[$province->code] = $province->name;
            }
        }
        return $this->provinces;
    }
}