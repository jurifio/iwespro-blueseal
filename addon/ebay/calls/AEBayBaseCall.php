<?php

namespace bamboo\addon\ebay\calls;

use bamboo\core\base\CToken;

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
abstract class AEBayBaseCall
{
    protected $messageId;
    protected $headers;
	protected $environmentData;

	/**
	 * AEBayBaseCall constructor.
	 * @param array $environmentData
	 * @param array $callData
	 */
    public function __construct(array $environmentData, array $callData = [])
    {
        $this->messageId = new CToken(8);
	    $this->environmentData = $environmentData;
	    $this->callData = $callData;
    }

	protected abstract function build();

	private function getWriter($indent = null)
	{
		$x = new \XMLWriter();
		if(!is_bool($indent) && isset($this->environmentData['indent'])){
			$indent = $this->environmentData['indent'];
		} else {
			$indent = true;
		}
		$x->setIndent($indent);
		return $x;
	}
	/**
	 * @return string
	 */
	protected function buildStandardInput()
	{
		$x = $this->getWriter();
		$x->writeElement('ErrorLanguage',$this->environmentData['ErrorLanguage']);
		$x->writeElement('MessageID',$this->messageId);
		$x->writeElement('Version',$this->environmentData['Version']);
		$x->writeElement('WarningLevel',$this->environmentData['WarningLevel']);
		return $x->outputMemory();
	}

	public function getRaw() {
		return $this->build();
	}

	public abstract function digestResponse($rawResponse);
}