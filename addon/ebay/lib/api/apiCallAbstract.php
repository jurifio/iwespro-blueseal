<?php

namespace bamboo\app\business\api;

/**
 * Class apiCallAbstract
 * @package redpanda\app\business\api
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 18/01/2016
 * @since 1.0
 */
abstract class apiCallAbstract
{
    /**
     * @var string
     */
    protected $xml;

    /**
     * @param $xml
     */
    protected function setXML($xml)
    {
        $this->xml = new \SimpleXMLElement($xml);
    }

    /**
     * @param $array
     * @param \SimpleXMLElement $xml
     * @return mixed
     */
    protected function array_to_xml($array, \SimpleXMLElement &$xml)
    {
        foreach($array as $key => $value) {
            if (is_array($value)) {
                $key = is_numeric($key) ? "item$key" : $key;
                $subnode = $xml->addChild("$key");
                $this->array_to_xml($value, $subnode);
            } else {
                $key = is_numeric($key) ? "item$key" : $key;
                $xml->addChild("$key","$value");
            }
        }

        return $xml->asXML();
    }
}