<?php

namespace redpanda\blueseal\ebay;
use redpanda\core\exceptions\RedPandaEbayException;

/**
 * Class CEBayAPIClient
 * @package redpanda\blueseal\ebay
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/02/2016
 * @since 1.0
 */
class CEBayAPIClient
{
    public $user;
    public $eBayApp;
    public $env;

    /**
     * CEBayAPIClient constructor.
     * @param CEBayApplication $eBayApp
     * @param string $env
     */
    public function __construct(CEBayApplication $eBayApp, $env = 'S')
    {
        $this->eBayApp = $eBayApp;
        $this->setEnv($env);
    }

    /**
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @param string $env
     * @throws RedPandaEbayException
     */
    public function setEnv($env)
    {
        if (in_array(['S','P'],$env)) {
            $this->env = $env;
        } else {
            throw new RedPandaEbayException('Invalid environment (Allowed values are S for Sandbox or P for Production), you provided %s',[$env],99180);
        }
    }
}