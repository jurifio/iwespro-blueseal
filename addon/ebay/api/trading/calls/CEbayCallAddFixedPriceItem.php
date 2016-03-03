<?php

namespace bamboo\addon\ebay\api\trading\calls;

use bamboo\addon\ebay\trading\AEbayTradingCall;

/**
 * Class CEbayCallAddFixedPriceItem
 * @package bamboo\addon\ebay\api\trading\calls
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/03/2016
 * @since 1.0
 */
class CEbayCallAddFixedPriceItem extends AEbayTradingCall
{
	protected function build($indent = false) {
		$x = new \XMLWriter();
		$x->setIndent($indent);
		$x->startDocument($this->xmlVersion,$this->encoding);
	}
}