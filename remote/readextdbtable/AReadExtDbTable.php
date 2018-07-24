<?php


namespace bamboo\blueseal\remote\readextdbtable;

use bamboo\core\base\CConfig;
use bamboo\core\db\pandaorm\adapter\CMySQLStandAloneAdapter;
use bamboo\core\exceptions\BambooConfigException;
use bamboo\domain\entities\CNewsletterShop;


/**
 * Class AReadExtDbTable
 * @package bamboo\blueseal\remote\readclientuser
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 23/07/2018
 * @since 1.0
 */
class AReadExtDbTable
{


    /**	@var CConfig */
    protected $config;
    protected $client;
    protected $destination;
    protected $dbName;
    protected $ownersFields = [];

    /**
     * AReadExtDbTable constructor.
     * @param int $shop
     * @throws BambooConfigException
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function __construct(int $shop)
    {
        $this->readConfig($shop);
        $config = $this->config->fetchAll('origin');
        $this->destination = \Monkey::app()->dbAdapter;

        $this->dbName = $config["name"];
        $this->client = new CMySQLStandAloneAdapter(
            \Monkey::app(),
            $config['engine'],
            $config['host'],
            $config['name'],
            $config['charset'],
            $config['user'],
            $config['pass']);
        $this->client->connect();


    }

    /**
     * @param $shop
     * @throws BambooConfigException
     */
    public function readConfig($shop)
    {

        /** @var CNewsletterShop $newsletterShop */
        $newsletterShop = \Monkey::app()->repoFactory->create('NewsletterShop')->findOneBy(["id" => $shop]);
        $shopName = $newsletterShop->name;
        $filePath = __DIR__ . '/config';
        $filePath .= '/' . strtolower($shopName) . '.json';

        if (!file_exists($filePath)) throw new BambooConfigException('Configuration not found in: ' . $filePath);

        $this->config = new CConfig($filePath);
        $this->config->load();
    }

    /**
     * @param $tableName
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    protected function getTableFields($tableName){
        $res = \Monkey::app()->dbAdapter->query("SHOW COLUMNS FROM ".$tableName, [])->fetchAll();
        foreach($res as $v) {
            $this->ownersFields[$tableName][] = $v["Field"];
        }
    }

}