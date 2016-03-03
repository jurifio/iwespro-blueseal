<?php

namespace bamboo\app\business;

use bamboo\core\application\AApplication;
use bamboo\core\base\CConfig;

/**
 * Class eBay
 * @package bamboo\app\business
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 18/01/2016
 * @since 1.0
 * @deprecated
 */
class eBay
{
    /**
     * @var string
     */
    private $token;
    /**
     * @var CConfig
     */
    private $config;

    /**
     * @var string
     */
    private $api;

    /**
     * eBay constructor.
     * @param eBaySeller $seller
     * @param AApplication $app
     */
    public function __construct(eBaySeller $seller, AApplication $app)
    {
        $this->config = new CConfig($app->cfg()->fetch('paths','blueseal-addon').'/ebay/config/cfg.json');
        $this->token = $seller->getToken();
    }

    /**
     * @param string $api
     * @return $this
     */
    public function useApi($api = 'trading')
    {
        $this->api = $api;
        return $this;
    }

    /**
     * Makes an api call
     *
     * @param string $method
     * @param array $params
     * @param null $country
     * @return mixed
     */
    public function call($method, array $params = [], $country = null)
    {
        $method = 'api\\'.$method;
        $api = new $method($this->config);
        $api->pass($params, $this->token, $country);
        return $api->execute();
    }
}