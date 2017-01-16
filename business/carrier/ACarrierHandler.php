<?php

namespace bamboo\business\carrier;

abstract class ACarrierHandler {

    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public abstract function addDelivery($source,$dest,$date,$notes);
}