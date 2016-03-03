<?php

namespace bamboo\addon\ebay\trading;

use bamboo\addon\ebay\core\AXMLApiCall;

/**
 * Class AEbayTradingCall
 * @package bamboo\addon\ebay\trading
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
abstract class AEbayTradingCall
{
	/** @var string */
	protected $errorLanguage;
	/** @var string */
	protected $messageId;
	/** @var string */
	protected $version;
	/** @var CEbayWarningLevelCodeType */
	protected $warningLevel;
}