<?php


namespace bamboo\addon\ebay;
use bamboo\addon\ebay\calls\AEBayBaseCall;


/**
 * Class CEBayCallGetSessionID
 * @package bamboo\addon\ebay
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, 01/03/2016
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 * @deprecated
 */
class CEBayCallGetSessionID extends AEBayBaseCall
{

	protected function build(){

		$x = $this->getWriter();
		$x->startDocument('1.0','utf-8');
		$x->startElement('GetSessionIDRequest');
		$x->writeAttribute('xmlns','urn:ebay:apis:eBLBaseComponents');

		$x->writeElement('RuName',$this->environmentData['RuName']);

		$x->writeRaw($this->buildStandardInput());

		$x->endElement();
		return $x->outputMemory();
	}


}