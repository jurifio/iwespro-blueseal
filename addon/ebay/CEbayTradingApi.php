<?php

namespace redpanda\blueseal\ebay;

use redpanda\blueseal\ebay\calls\trading\CGetSessionId;
use redpanda\core\exceptions\RedPandaEbayException;

/**
 * Class CEBayTradingApi
 * @package redpanda\blueseal\ebay
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
class CEBayTradingApi
{
    /**
     * @var CEBayAPIClient
     */
    protected $client;
    protected $errorLanguage;
    protected $warningLevel;
    protected $endpoint;
    protected $version;
    protected $encoding;
    protected $xmlVersion;
    protected $xmlNs;

    /**
     * CEBayTradingApi constructor.
     * @param CEBayAPIClient $client
     */
    public function __construct(CEBayAPIClient $client)
    {
        $cfg = json_decode(file_get_contents('calls/trading/cfg/cfg.json'));
        $this->client = $client;
        $this->encoding = $cfg->encoding;
        $this->xmlVersion = $cfg->xmlVersion;
        $this->xmlNs = $cfg->xmlNs;
        $this->env = $this->client->getEnv();
        switch ($this->env) {
            case 'S':
                $this->setEndpoint($cfg->sandbox);
                break;
            case 'P':
                $this->setEndpoint($cfg->production);
                break;
        }
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param string $endpoint
     * @throws RedPandaEbayException
     */
    public function setEndpoint($endpoint)
    {
        if (!is_null($endpoint) && $endpoint != '' && mb_stristr($endpoint,'https')) {
            $this->endpoint = $endpoint;
        } else {
            throw new RedPandaEbayException('Invalid API endpoint, you provided %s',[$endpoint],99130);
        }
    }

    /**
     * @return string
     */
    public function getWarningLevel()
    {
        return $this->warningLevel;
    }

    /**
     * @param string $warningLevel
     * @throws RedPandaEbayException
     */
    public function setWarningLevel($warningLevel)
    {
        if (in_array(['Low','High'],$warningLevel)) {
            $this->warningLevel = $warningLevel;
        } else {
            throw new RedPandaEbayException('Invalid Warning Level (Allowed values are Low and High), you provided %s',[$warningLevel],99120);
        }
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param int $version
     * @throws RedPandaEbayException
     */
    public function setVersion($version)
    {
        if (!is_null($version) && $version != '' && !is_numeric($version)) {
            $this->version = $version;
        } else {
            throw new RedPandaEbayException('Invalid API version number, you provided %s',[$version],99110);
        }
    }

    public function getSessionId()
    {
        $request = new CGetSessionId();
        return $request->send();
    }
}