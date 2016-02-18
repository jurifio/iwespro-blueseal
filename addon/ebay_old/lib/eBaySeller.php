<?php

namespace bamboo\app\business;

/**
 * Class eBaySeller
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
 */
class eBaySeller
{
    /**
     * @var string
     */
    private $token;

    /**
     * eBaySeller constructor.
     * @param $token
     */
    public function __construct($token)
    {
        $this->setToken($token);
    }

    /**
     * @param string $token
     */
    private function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}