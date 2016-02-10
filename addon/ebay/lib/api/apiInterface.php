<?php

namespace bamboo\app\business\api;

/**
 * Interface apiInterface
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
interface apiInterface
{
    public function pass(array $params, $token, $country);
    public function execute();
}