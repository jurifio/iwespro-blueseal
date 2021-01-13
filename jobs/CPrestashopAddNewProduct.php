<?php
namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CPrestashopHasProduct;

/**
 * Class CPrestashopAddNewProduct
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/03/2019
 * @since 1.0
 */
class CPrestashopAddNewProduct extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->insertNewProductsInPrestashop();
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    private function insertNewProductsInPrestashop()
    {
        $phpRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');

        $prestashopProduct = new CPrestashopProduct();
        $reservedIds = \Monkey::app()->dbAdapter->query("SELECT DISTINCT marketplaceHasShopId , modifyType, variantValue  FROM PrestashopHasProduct WHERE marketplaceHasShopId IS NOT NULL and `status`=0", [])->fetchAll();

        foreach ($reservedIds as $reservedId){

           // $prodChar = explode('|', $reservedId['prodChar']);

            /** @var CMarketplaceHasShop $mhs */
            $mhs = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOneBy(['id' => $reservedId['marketplaceHasShopId']]);

            /** @var CObjectCollection $phpCollection */
            $phpCollection = $phpRepo->findBy(['marketplaceHasShopId'=>$reservedId['marketplaceHasShopId'], 'modifyType'=>$reservedId['modifyType'], 'variantValue'=>$reservedId['variantValue']]);

            $products = new CObjectCollection();

            /** @var CPrestashopHasProduct $php */
            foreach ($phpCollection as $php){
                $products->add($php->product);
            }
            try {
               // if ($prestashopProduct->addNewProducts($products,$mhs,$reservedId['modifyType'],$reservedId['variantValue'])) {
                    \Monkey::app()->dbAdapter->query('UPDATE PrestashopHasProduct 
                                                        SET 
                                                          marketplaceHasShopId = NULL, 
                                                          modifyType = NULL,
                                                          variantValue = NULL
                                                         WHERE 
                                                          marketplaceHasShopId = ?
                                                          AND modifyType = ?
                                                          AND variantValue = ?',
                        [$reservedId['marketplaceHasShopId'],$reservedId['modifyType'],$reservedId['variantValue']]);
                //}
            }catch(\Throwable $e){
                $this->report('Export product','CPrestashopAddNewProduct', $e->getMessage.' '.$e->getLine());
            }
        }

        $this->report('Export product', 'End Export');
    }
}