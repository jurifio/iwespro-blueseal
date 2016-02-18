<?php

namespace redpanda\blueseal\ebay;

use redpanda\app\domain\entities\CEBayApp;
use redpanda\core\application\AApplication;
use redpanda\core\exceptions\RedPandaEbayException;

/**
 * Class CEBayApplication
 * @package redpanda\blueseal\ebay
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/02/2016
 * @since 1.0
 */
class CEBayApplication
{
    protected $ruName;
    protected $appId;
    protected $devId;
    protected $certId;

    /**
     * CEBayApplication constructor.
     * @param AApplication $app
     * @param string $env
     * @throws RedPandaEbayException
     */
    public function __construct(AApplication $app, $env)
    {
        $this->app = $app;
        $em = $this->app->entityManagerFactory->create('EbayApp');
        $ebayApp = $em->findOne(['env'=>$env]);

        if ($ebayApp instanceof CEBayApp) {
            $this->setRuName($ebayApp->ruName);
            $this->setAppId($ebayApp->appid);
            $this->setDevId($ebayApp->devid);
            $this->setCertId($ebayApp->certid);
        } else {
            throw new RedPandaEbayException('EBay Application with id %s does not exist',[$id],99100);
        }
    }

    /**
     * @return string
     */
    public function getRuName()
    {
        return $this->ruName;
    }

    /**
     * @param string $ruName
     * @throws RedPandaEbayException
     */
    public function setRuName($ruName)
    {
        if (preg_match('/[0-9a-zA-Z_]+\-[0-9A-Za-z]{8}-[0-9a-z]{4}-[0-9]{1}-[a-z]{8}/',$ruName)) {
            $this->ruName = $ruName;
        } else {
            throw new RedPandaEbayException('Invalid RuName, you provided %s',[$ruName],99170);
        }
    }

    /**
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param string $appId
     * @throws RedPandaEbayException
     */
    public function setAppId($appId)
    {
        if (preg_match('/[0-9a-zA-Z]{8}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{12}/',$appId)) {
            $this->appId = $appId;
        } else {
            throw new RedPandaEbayException('Invalid Application ID, you provided %s',[$appId],99160);
        }
    }

    /**
     * @return string
     */
    public function getDevId()
    {
        return $this->devId;
    }

    /**
     * @param string $devId
     * @throws RedPandaEbayException
     */
    public function setDevId($devId)
    {
        if (preg_match('/[0-9a-z]{8}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{12}/',$devId)) {
            $this->devId = $devId;
        } else {
            throw new RedPandaEbayException('Invalid Developer ID, you provided %s',[$devId],99150);
        }
    }

    /**
     * @return string
     */
    public function getCertId()
    {
        return $this->certId;
    }

    /**
     * @param string $certId
     * @throws RedPandaEbayException
     */
    public function setCertId($certId)
    {
        if (preg_match('/[0-9a-z]{8}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{12}/',$certId)) {
            $this->certId = $certId;
        } else {
            throw new RedPandaEbayException('Invalid Certificate ID, you provided %s',[$certId],99140);
        }
    }
}