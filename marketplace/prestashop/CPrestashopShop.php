<?php


namespace bamboo\blueseal\marketplace\prestashop;
use bamboo\core\exceptions\BambooException;

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
     * @param $name
     * @return bool
     */
    public function addNewShop($name){

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
            $this->ws->add($opt);
        } catch (\Throwable $e){
            \Monkey::app()->applicationLog('CPrestashopShop', 'error', 'Error while insert new shop', $e->getMessage());
            return false;
        }

        //add category for new shop
        $prestashopCategory = new CPrestashopCategory();
        $prestashopCategory->updateAllCategoriesWithShopGroup();

        //add manufacturers for new shop
        $prestashopManufacturers = new CPrestashopManufacturer();
        $prestashopManufacturers->updateAllManufacturersWithShopGroup();

        //add attributes for new shop
        $prestashopOptionValues = new CPrestashopProductOptionValues();
        $prestashopOptionValues->updateAllProductOptionsWithShopGroup();
        $prestashopOptionValues->updateAllProductOptionValuesWithShopGroup();

        //add features for new shop
        $prestashopFeatures = new CPrestashopFeatures();
        $prestashopFeatures->updateAllFeaturesWithShopGroup();

        return true;
    }

}