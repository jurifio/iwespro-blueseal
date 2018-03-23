<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CShooting;
use bamboo\domain\repositories\CProductHasShootingRepo;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CShootingRepo;


/**
 * Class CProductShootingAjaxController
 * @package bamboo\controllers\back\ajax
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
class CProductShootingAjaxController extends AAjaxController
{
    /**
     * @return string
     */
    public function post()
    {
        $res = "";
        $data = \Monkey::app()->router->request()->getRequestData();

        $friendDdt = $data["friendDdt"];
        $note = $data["note"];
        $productsIds = $data["products"];
        $shopId = $data["friendId"];

        if(empty($friendDdt)){
            $res = "Devi inserire il ddt del friend";
            return $res;
        }

        //creo shooting
        /** @var CShootingRepo $shootingRepo */
        $shootingRepo = \Monkey::app()->repoFactory->create('Shooting');

        /** @var CShooting $existingShooting */
        //$existingShooting = $shootingRepo->findOneBy(['friendDdt'=>$friendDdt, 'shopId'=>$shopId]);
        $existingShooting = $shootingRepo->findOneBySql("SELECT *
                                                              FROM Shooting s
                                                              WHERE s.friendDdt = ? AND 
                                                              s.shopId = ?", [$friendDdt, $shopId]);

        if(is_null($existingShooting)){
            $shootingId = $shootingRepo->createShooting($productsIds, $friendDdt, $note, $shopId);
            $res = "Hai inserito correttamente i prodotti nello shooting con codice: ".$shootingId;
        } else {

            $shootingId = $existingShooting->id;

            /** @var CProductHasShootingRepo $phsRepo */
            $phsRepo = \Monkey::app()->repoFactory->create('ProductHasShooting');
            if($phsRepo->associateNewProductsToShooting($productsIds, $shootingId)){
                $res = "Hai aggiornato correttamente i prodotti nello shooting con codice: ".$shootingId;
            };
        }

        return $res;
    }

}