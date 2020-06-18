<?php

namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSku;
use PDO;
use bamboo\amazon\business\builders\AAmazonFeedBuilder;
use bamboo\amazon\business\builders\CAmazonImageFeedBuilder;
use bamboo\amazon\business\builders\CAmazonInventoryFeedBuilder;
use bamboo\amazon\business\builders\CAmazonPricingFeedBuilder;
use bamboo\amazon\business\builders\CAmazonProductFeedBuilder;
use bamboo\amazon\business\builders\CAmazonRelationshipFeedBuilder;
use bamboo\core\application\AApplication;
use bamboo\domain\entities\CMarketplaceAccountHasProduct;
use bamboo\domain\entities\CProductPhoto;

/**
 * Class CCreateProductCorrelationJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 18/06/2020
 * @since 1.0
 */
class CCreateProductCorrelationJob extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->createProductCorrelation();
        \Monkey::app()->vendorLibraries->load('amazonMWS');
    }


    private function createProductCorrelation()
    {
        try {
            ini_set('memory_limit', '1024M');
            $productCorrelationRepo = \Monkey::app()->repoFactory->create('ProductCorrelation');
            $productHasProductCorrelationRepo = \Monkey::app()->repoFactory->create('ProductHasProductCorrelation');
            $productRepo = \Monkey::app()->repoFactory->create('Product');
            $shopHasProductRepo = \Monkey::app()->repoFactory->create('ShopHasProduct');
            $products = $productRepo->findBy(['stored'=>0]);


            foreach ($products as $product) {
                $shopHasProduct = $shopHasProductRepo->findBy(['productId' => $product->id]);
                $findProductCorrelation = $productCorrelationRepo->findOneBy(['code' => 'C0LOUR','name' => $product->id]);
                if ($findProductCorrelation == null) {
                    $findProductCorrelationInsert = $productCorrelationRepo->getEmptyEntity();
                    $findProductCorrelationInsert->name = $product->id;
                    $findProductCorrelationInsert->description = 'varianti taglie ' . $product->id;
                    $findProductCorrelationInsert->note = 'varianti taglie ' . $product->id;
                    $findProductCorrelationInsert->code = 'COLOUR';
                    $photo = \Monkey::app()->repoFactory->create('ProductPhoto')->getPhotoForProductSizeOrder($product, 281, 1);
                    $photoImage=($photo)? 'https://cdn.iwes.it/'.$product->productBrand->slug . '/' . $photo->name: 'https://cdn.iwes.it/dummy/bs-dummy-16-9.png';
                    $findProductCorrelationInsert->image = $photoImage;
                    $findProductCorrelationInsert->seo = 'varianti taglie ' . $product->id;
                    $findProductCorrelationInsert->insert();
                    $res = \Monkey::app()->dbAdapter->query('select max(id) as id from ProductCorrelation ',[])->fetchAll();
                    foreach ($res as $result) {
                        $lastId = $result['id'];
                    }
                    foreach ($shopHasProduct as $pr) {
                        $findProductHasProductCorrelation = $productHasProductCorrelationRepo->findOneBy(['productId' => $pr->productId,
                            'productVariantId' => $pr->productVariantId,
                            'shopId' => $pr->shopId,
                            'correlationId' => $lastId]);
                        if ($findProductHasProductCorrelation == null) {
                            $findProductHasProductCorrelationInsert = $productHasProductCorrelationRepo->getEmptyEntity();
                            $findProductHasProductCorrelationInsert->correlationId = $lastId;
                            $findProductHasProductCorrelationInsert->productId = $pr->productId;
                            $findProductHasProductCorrelationInsert->productVariantId = $pr->productVariantId;
                            $findProductHasProductCorrelationInsert->shopId = $pr->shopId;
                            $findProductHasProductCorrelationInsert->insert();

                        } else {
                            continue ;
                        }
                    }


                } else {
                    continue;
                }


            }
        }catch(\Throwable $e){
            $this->report('CCreateProductCorrelationJob',$e->getMessage(),$e->getLine());
        }


    }
    /**
     * @param CProduct $product
     * @param $size
     * @param $order
     * @return \bamboo\core\db\pandaorm\entities\AEntity|null
     */
    private function getPhotoForProductSizeOrder(CProduct $product, $size, $order)
    {
        $sql = "SELECT id 
                FROM ProductHasProductPhoto phpp JOIN 
                  ProductPhoto pp ON phpp.productPhotoId = pp.id
                WHERE phpp.productId = ? AND 
                      phpp.productVariantId = ? AND 
                      pp.size = ? AND 
                      pp.`order` = ?";
        return $this->em()->findBySql($sql, [$product->id, $product->productVariantId, $size, $order])->getFirst();
    }


}