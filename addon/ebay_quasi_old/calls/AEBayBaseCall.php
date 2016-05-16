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
 * @deprecated
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

	protected function getWriter($indent = null)
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

	public function digestResponse($rawResponse)
	{
		// TODO: Implement digestResponse() method.
	}

	/**
	 *
	 */
	public function call() {
		//build eBay headers using variables passed via constructor
		$headers = array (
			//Regulates versioning of the XML interface for the API
			'X-EBAY-API-COMPATIBILITY-LEVEL: ' . $this->compatLevel,

			//set the keys
			'X-EBAY-API-DEV-NAME: ' . $this->devID,
			'X-EBAY-API-APP-NAME: ' . $this->appID,
			'X-EBAY-API-CERT-NAME: ' . $this->certID,

			//the name of the call we are requesting
			'X-EBAY-API-CALL-NAME: ' . $this->verb,

			//SiteID must also be set in the Request's XML
			//SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
			//SiteID Indicates the eBay site to associate the call with
			'X-EBAY-API-SITEID: ' . $this->siteID,
		);

		//initialise a CURL session
		$connection = curl_init();
		//set the server we are using (could be Sandbox or Production server)
		curl_setopt($connection, CURLOPT_URL, $this->serverUrl);

		//stop CURL from verifying the peer's certificate
		curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);

		//set the headers using the array of headers
		curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);

		//set method as POST
		curl_setopt($connection, CURLOPT_POST, 1);

		//set the XML body of the request
		curl_setopt($connection, CURLOPT_POSTFIELDS, $this->getRaw());

		//set it to return the transfer as a string from curl_exec
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

		//Send the Request
		$response = curl_exec($connection);

		//close the connection
		curl_close($connection);

		//return the response
		return $response;
	}
}