<?php


namespace bamboo\blueseal\marketplace\prestashop;

use bamboo\core\base\CConfig;
use bamboo\core\exceptions\BambooConfigException;


/**
 * Class APrestashopMarketplace
 * @package bamboo\blueseal\remote\prestashopmarketplace
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 23/01/2019
 * @since 1.0
 */
abstract class APrestashopMarketplace
{

    protected $url;
    protected $key;
    protected $debug;
    /**	@var CConfig */
    protected $config;

    protected $ws;
    protected $resource;

    /**
     * APrestashopMarketplace constructor.
     * @throws BambooConfigException
     */
    public function __construct()
    {
        $this->readConfig();
        $fullClassName = get_class($this);
        $partClassName = $id = substr($fullClassName, strrpos($fullClassName, '\\') + 1);

        $configSection = null;

        switch ($partClassName){
            case 'CPrestashopCategory':
                $configSection = 'category';
                break;
            case 'CPrestashopShop':
                $configSection = 'shop';
                break;
            case 'CPrestashopProduct':
                $configSection = 'product';
                break;
            case 'CPrestashopManufacturer':
            $configSection = 'manufacturer';
            break;
        }

        $configConstructor = $this->config->fetchAll($configSection);
        $this->url = $configConstructor['url'];
        $this->key = $configConstructor['key'];
        $this->debug = $configConstructor['debug'];
        $this->resource = $configConstructor['resource'];

        switch (ENV) {
            CASE 'dev':
                require_once __DIR__ . '/test/PSWebServiceLibrary.php';
                $this->ws = new \PrestaShopWebserviceTest($this->url, $this->key, $this->debug);
                break;
            default:
                $this->ws = new \PrestaShopWebserviceTest($this->url, $this->key, $this->debug);
                break;
        }
    }

    /**
     * @throws BambooConfigException
     */
    private function readConfig()
    {
        $filePath = __DIR__ . '/config/' . ENV . '.' . 'prestashop.json';

        if (!file_exists($filePath)) throw new BambooConfigException('Configuration not found in: ' . $filePath);

        $this->config = new CConfig($filePath);
        $this->config->load();
    }

}