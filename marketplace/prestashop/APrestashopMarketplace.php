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
     * @throws \bamboo\core\exceptions\BambooException
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
            case 'CPrestashopProductOptionValues':
                $configSection = 'productOption';
                break;
            case 'CPrestashopFeatures':
                $configSection = 'features';
                break;
            case 'CPrestashopOrders':
                $configSection = 'orders';
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
                \Monkey::app()->vendorLibraries->load('prestashop');
                $this->ws = new \PrestaShopWebservice($this->url, $this->key, $this->debug);
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

    /**
     * @param string $resource
     * @return \SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function getBlankSchema(string $resource = ''): \SimpleXMLElement
    {
        $resource = empty($resource) ? $this->resource : $resource;
        return $this->ws->get(array('resource' => $resource . '/?schema=blank'));
    }

    /**
     * @param int $id
     * @param string $resource
     * @return \SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function getResourceFromId(int $id, string $resource = ''): \SimpleXMLElement
    {
        $correctResource = empty($resource) ? $this->resource : $resource;
        return $this->ws->get(array('resource' => $correctResource, 'id' => $id));
    }

    /**
     * @param $resource
     * @param null $id
     * @param array $filter
     * @param null $display
     * @param null $shopGroupId
     * @param null $shopId
     * @return \SimpleXMLElement
     * @throws \PrestaShopWebserviceException
     */
    public function getDataFromResource($resource, $id = null, array $filter = [], $display = null, $shopGroupId = null, $shopId = null, $date = null)
    {

        $opt['resource'] = $resource;

        if (!is_null($id)) $opt['id'] = $id;
        if (!empty($filter)) $opt['filter'] = $filter;
        if (!is_null($display)) $opt['display'] = $display;
        if (!is_null($shopGroupId)) $opt['id_group_shop'] = $shopGroupId;
        if (!is_null($shopId)) $opt['id_shop'] = $shopId;
        if (!is_null($date)) $opt['date'] = $date;

        return $this->ws->get($opt);
    }

}