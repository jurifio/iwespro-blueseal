<?php


namespace bamboo\blueseal\remote\readclientuser;

use bamboo\core\base\CConfig;
use bamboo\core\db\pandaorm\adapter\CMySQLStandAloneAdapter;
use bamboo\core\exceptions\BambooConfigException;


/**
 * Class AReadClientUser
 * @package bamboo\blueseal\remote\readclietuser
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/07/2018
 * @since 1.0
 */
class AReadClientUser
{


    /**	@var CConfig */
    protected $config;

    protected $client;


    /**
     * AReadClientUser constructor.
     * @param $shop
     * @throws BambooConfigException
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function __construct($shop)
    {
        $this->readConfig($shop);
        $config = $this->config->fetchAll('destination');

        $this->client = new CMySQLStandAloneAdapter(
            \Monkey::app(),
            $config['engine'],
            $config['host'],$config['name'],
            $config['charset'],
            $config['user'],
            $config['pass']);
        $this->client->connect();
    }

    /**
     * @throws BambooConfigException
     */
    public function readConfig($shop)
    {
        $filePath = __DIR__ . '/config';
        $filePath .= '/' . strtolower($shop) . '.json';

        if (!file_exists($filePath)) throw new BambooConfigException('Configuration not found for Importer: ' . $filePath);

        $this->config = new CConfig($filePath);
        $this->config->load();
    }

}