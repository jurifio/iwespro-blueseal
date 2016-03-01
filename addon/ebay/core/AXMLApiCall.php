<?php

namespace bamboo\addon\ebay\core;

/**
 * Class AXMLApiCall
 * @package bamboo\addon\ebay\core
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/03/2016
 * @since 1.0
 */
abstract class AXMLApiCall
{
    protected $compatibilityLevel;
    protected $callName;
    protected $ebaySiteId;
    protected $contentType;
    protected $contentLength;
}