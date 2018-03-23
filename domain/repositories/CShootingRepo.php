<?php

namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;

/**
 * Class CShootingRepo
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
class CShootingRepo extends ARepo
{

    public function createShooting($productsIds, $friendDdt, $note, $shopId){

        $shooting = $this->getEmptyEntity();
        $shooting->friendDdt = $friendDdt;
        $shooting->note = $note;
        $shooting->phase = "accepted";
        $shooting->shopId = $shopId;
        $shooting->smartInsert();

        /** @var CProductHasShootingRepo $pHsRepo */
        $pHsRepo = \Monkey::app()->repoFactory->create('ProductHasShooting');

        if($pHsRepo->associateNewProductsToShooting($productsIds, $shooting->id)) {
            return $shooting->id;
        }
    }

}