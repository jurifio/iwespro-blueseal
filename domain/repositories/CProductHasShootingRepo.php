<?php

namespace bamboo\domain\repositories;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductHasShooting;
use bamboo\domain\entities\CShooting;

/**
 * Class CProductHasShootingRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 23/03/2018
 * @since 1.0
 */
class CProductHasShootingRepo extends ARepo
{

    /**
     * @param $productsIds
     * @param $shootingId
     * @param $productsInformation
     * @return mixed
     */
    public function associateNewProductsToShooting($productsIds, $shootingId, $productsInformation){

        $z = 0;
        $lastShooting = null;
        $existentProduct = [];
        $infoWithExistentProduct = [];
        $info =[];

        $splittedInfo = [];
        $i = 0;
        $z = 0;
        foreach ($productsInformation as $val){

            $splittedInfo[$i][] = $val;
            $z++;
            if($z % 3 == 0 && $z != 0){
                $i++;
            }
        }


        foreach ($productsIds as $productsId){

            foreach ($splittedInfo as $singleProductInfo){

                if($singleProductInfo[0] == $productsId){
                    $sizeToInsert = $singleProductInfo[1];
                    $qtyToInsert = $singleProductInfo[2];
                }

            }

            $productId = explode('-',$productsId)[0];
            $productVariantId = explode('-',$productsId)[1];

            /** @var CProductHasShooting $existingAssociation */
            $existingAssociation = $this->findOneBySql("SELECT *
                                                            FROM Shooting s
                                                            JOIN ProductHasShooting phs ON s.id = phs.shootingId
                                                            WHERE phs.productId = ? AND 
                                                            phs.productVariantId = ? AND 
                                                            phs.shootingId = ?", [$productsId, $productVariantId, $shootingId]);

            if(!is_null($existingAssociation)){
                $existentProduct[] = $existingAssociation->productId.'-'.$existingAssociation->productVariantId;
                continue;
            }




            /** @var CObjectCollection $shootings */
            $shootings = $this->findBy(['shootingId'=>$shootingId]);

            /** @var CProductHasShooting $sps */
            $sps = $this->getEmptyEntity();
            $sps->productId = $productId;
            $sps->productVariantId = $productVariantId;
            $sps->shootingId = $shootingId;
            $sps->size = $sizeToInsert;
            $sps->qty = $qtyToInsert;
            $sps->progressiveLineNumber = ($shootings->isEmpty() ? 1 : $this->getLastProgressiveNumber($shootingId, true));
            $sps->smartInsert();

            $info[] = $this->getProductDetails($sps);
        }


        $infoProduct["info"] = $info;
        $infoProduct["existent"] = $existentProduct;
        return $infoProduct;


    }

    private function getProductDetails(CProductHasShooting $phs) : array {

        $info = [];

        /** @var CProductRepo $productRepo */
        $productRepo = \Monkey::app()->repoFactory->create('Product');

        /** @var CProduct $product */
        $product = $productRepo->findOneBy(['id'=>$phs->productId, 'productVariantId'=>$phs->productVariantId]);

        $info[] = $product->id.'-'.$product->productVariantId;
        $info[] = $product->itemno;
        $info[] = $product->getShopExtenalIds(', ');
        $info[] = $product->productBrand->name;
        $info[] = $phs->progressiveLineNumber;

        return $info;
    }

    public function forceInsertProduct(CProductHasShooting $productHasShooting){

        $newPhs = $this->getEmptyEntity();
        $newPhs->productId = $productHasShooting->productId;
        $newPhs->productVariantId = $productHasShooting->productVariantId;
        $newPhs->shootingId = $productHasShooting->shootingId;
        $newPhs->progressiveLineNumber = $this->getLastProgressiveNumber($productHasShooting->shootingId, true);
        $newPhs->smartInsert();

        return true;
    }

    public function getLastProgressiveNumber($shootingId, $increment = false){

        $lpn = \Monkey::app()->dbAdapter->query("SELECT max(ps.progressiveLineNumber) as max
                                                        FROM ProductHasShooting ps
                                                        WHERE shootingId = ?", [$shootingId])->fetch();

        $res = ($increment ? $lpn["max"] + 1 : $lpn["max"]);

        return $res;

    }

    public function array_unique_multidimensional($input)
    {
        $serialized = array_map('serialize', $input);
        $unique = array_unique($serialized);
        return array_intersect_key($input, $unique);
    }
}