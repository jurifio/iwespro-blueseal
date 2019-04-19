<?php


namespace bamboo\blueseal\marketplace\prestashop;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CMarketplaceHasShop;

/**
 * Class CPrestashopShop
 * @package bamboo\blueseal\marketplace\prestashop
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 11/02/2019
 * @since 1.0
 */

class CPrestashopShop extends APrestashopMarketplace
{

    CONST SHOP_RESOURCE = 'shops';
    /**
     * @return array
     * @throws BambooException
     * @throws \PrestaShopWebserviceException
     */
    public function getAllPrestashopShops() : array {


       /** @var \SimpleXMLElement $shopsXml */
       $shopsXml = $this->ws->get(array('resource' => 'shops'));

       if($shopsXml instanceof \SimpleXMLElement){
           $ids = [];

           foreach ($shopsXml->children()->children() as $shopAtt){
               $ids[] = (int)$shopAtt['id'];
           }

           return $ids;
       } else throw new BambooException('Error while retriving shops');

    }

    /**
     * @param String $name
     * @param int $shopId
     * @param int $marketplaceId
     * @return bool
     */
    public function addNewShop(String $name, int $shopId, int $marketplaceId){

        \Monkey::app()->applicationLog('CPrestashopShop', 'log', 'Init', '');

        try {
            $shopBlankXml = $this->getBlankSchema('shops');

            $shopResource = $shopBlankXml->children()->children();
            $shopResource->id_shop_group = 1;
            $shopResource->id_category = 1;
            $shopResource->active = 1;
            $shopResource->deleted = 0;
            $shopResource->name = $name;

            $opt['resource'] = $this::SHOP_RESOURCE;
            $opt['postXml'] = $shopBlankXml->asXML();
            $xmlShopXml = $this->ws->add($opt);
        } catch (\Throwable $e){
            \Monkey::app()->applicationLog('CPrestashopShop', 'error', 'Error while insert new shop', $e->getMessage());
            return false;
        }

        \Monkey::app()->applicationLog('CPrestashopShop', 'log', 'Init Category', '');
        //add category for new shop
        $prestashopCategory = new CPrestashopCategory();
        $prestashopCategory->updateAllCategoriesWithShopGroup();
        \Monkey::app()->applicationLog('CPrestashopShop', 'log', 'End Category', '');


        \Monkey::app()->applicationLog('CPrestashopShop', 'log', 'Init Manufacturers', '');
        //add manufacturers for new shop
        $prestashopManufacturers = new CPrestashopManufacturer();
        $prestashopManufacturers->updateAllManufacturersWithShopGroup();
        \Monkey::app()->applicationLog('CPrestashopShop', 'log', 'End Manufacturers', '');

        \Monkey::app()->applicationLog('CPrestashopShop', 'log', 'Init Attributes', '');
        //add attributes for new shop
        $prestashopOptionValues = new CPrestashopProductOptionValues();
        $prestashopOptionValues->updateAllProductOptionsWithShopGroup();
        $prestashopOptionValues->updateAllProductOptionValuesWithShopGroup();
        \Monkey::app()->applicationLog('CPrestashopShop', 'log', 'End Attributes', '');

        \Monkey::app()->applicationLog('CPrestashopShop', 'log', 'Init Features', '');
        //add features for new shop
        $prestashopFeatures = new CPrestashopFeatures();
        $prestashopFeatures->updateAllFeaturesWithShopGroup();
        \Monkey::app()->applicationLog('CPrestashopShop', 'log', 'End Features', '');


        $prestashopId = (int)$xmlShopXml->children()->children()->id;

        /** @var CMarketplaceHasShop $mhs */
        $mhs = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->getEmptyEntity();
        $mhs->shopId = $shopId;
        $mhs->marketplaceId = $marketplaceId;
        $mhs->prestashopId = $prestashopId;
        $mhs->smartInsert();

        return true;
    }

}