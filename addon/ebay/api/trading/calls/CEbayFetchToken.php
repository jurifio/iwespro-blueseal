<?php

namespace bamboo\addon\ebay\trading\calls;

use bamboo\addon\ebay\api\trading\calls\types\CEbayItemType;
use bamboo\addon\ebay\trading\AEbayTradingCall;
use bamboo\addon\ebay\trading\CEbayWarningLevelCodeType;

/**
 * Class CEbayFetchToken
 * @package bamboo\addon\ebay\trading\calls
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
class CEbayFetchToken extends AEbayTradingCall
{
	/** @var string */
	protected $secretId;
	/** @var string */
	protected $sessionId;
	/** @var  CEbayItemType */
	protected $item;

	/**
	 * @return string
	 */
	public function getErrorLanguage()
	{
		return $this->errorLanguage;
	}

	/**
	 * @param string $errorLanguage
	 */
	public function setErrorLanguage($errorLanguage)
	{
		$this->errorLanguage = $errorLanguage;
	}

	/**
	 * @return string
	 */
	public function getMessageId()
	{
		return $this->messageId;
	}

	/**
	 * @param string $messageId
	 */
	public function setMessageId($messageId)
	{
		$this->messageId = $messageId;
	}

	/**
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * @param string $version
	 */
	public function setVersion($version)
	{
		$this->version = $version;
	}

	/**
	 * @return CEbayWarningLevelCodeType
	 */
	public function getWarningLevel()
	{
		return $this->warningLevel;
	}

	/**
	 * @param CEbayWarningLevelCodeType $warningLevel
	 */
	public function setWarningLevel($warningLevel)
	{
		$this->warningLevel = $warningLevel;
	}

	/**
	 * @return string
	 */
	public function getSecretId()
	{
		return $this->secretId;
	}

	/**
	 * @param string $secretId
	 */
	public function setSecretId($secretId)
	{
		$this->secretId = $secretId;
	}

	/**
	 * @return string
	 */
	public function getSessionId()
	{
		return $this->sessionId;
	}

	/**
	 * @param string $sessionId
	 */
	public function setSessionId($sessionId)
	{
		$this->sessionId = $sessionId;
	}

	/**
	 * @return CEbayItemType
	 */
	public function getItem()
	{
		return $this->item;
	}

	/**
	 * @param CEbayItemType $item
	 */
	public function setItem(CEbayItemType $item)
	{
		$this->item = $item;
	}
}