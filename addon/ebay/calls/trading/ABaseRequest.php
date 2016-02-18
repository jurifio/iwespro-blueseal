<?php

namespace redpanda\blueseal\ebay\calls\trading;

use redpanda\core\base\CToken;

/**
 * Abstract class ABaseRequest
 * @package redpanda\blueseal\ebay\calls\trading
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
abstract class ABaseRequest
{
    protected $messageId;
    protected $headers;

    public function __construct()
    {
        $this->messageId = new CToken(8);
    }
}