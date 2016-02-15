<?php

namespace bamboo\app\business\api;
use bamboo\core\base\CConfig;

/**
 * Class getOrders
 * @package bamboo\app\business\api
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
class getOrders extends tradingApiCallAbstract implements apiInterface
{
    public function __construct(CConfig $config)
    {
        $config['iam'] = 'GetOrders';
        $this->setConfig($config);
        $this->setXML(file_get_contents('api/xml/getOrders.xml'));
    }
}