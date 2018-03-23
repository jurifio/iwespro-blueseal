<?php

namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;

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

    public function associateNewProductsToShooting($productsIds, $shootingId){


        foreach ($productsIds as $productsId){

            $productId = explode('-',$productsId)[0];
            $productVariantId = explode('-',$productsId)[1];

            //$existingAssociation = $this->findOneBy(['shootingId'=>$shootingId, 'productId'=>$productId, 'productVariantId'=>$productVariantId]);
            $existingAssociation = $this->findOneBySql("SELECT *
                                                            FROM Shooting s
                                                            JOIN ProductHasShooting phs ON s.id = phs.shootingId
                                                            WHERE phs.productId = ? AND 
                                                            phs.productVariantId = ? AND 
                                                            phs.shootingId = ?", [$productsId, $productVariantId, $shootingId]);

            if(!is_null($existingAssociation)){
                continue;
            }

            $sps = $this->getEmptyEntity();
            $sps->productId = $productId;
            $sps->productVariantId = $productVariantId;
            $sps->shootingId = $shootingId;
            $sps->smartInsert();
        }
        return true;
    }
}