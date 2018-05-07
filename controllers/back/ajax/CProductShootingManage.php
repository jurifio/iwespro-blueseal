<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductHasShooting;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CContractDetailsRepo;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CProductHasShootingRepo;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CSectionalRepo;
use bamboo\domain\repositories\CUserRepo;


/**
 * Class CProductShootingManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 07/05/2018
 * @since 1.0
 */
class CProductShootingManage extends AAjaxController
{
    /**
     * @return bool
     */
    public function delete()
    {

        $products = \Monkey::app()->router->request()->getRequestData('products');
        $shootingId = \Monkey::app()->router->request()->getRequestData('shootingId');

        /** @var CProductHasShootingRepo $phs */
        $phs = \Monkey::app()->repoFactory->create('ProductHasShooting');

        $phs->deleteProductsFromShooting($products, $shootingId);

        return true;
    }


    /**
     * @return bool
     */
    public function put() : bool {

        $infos = \Monkey::app()->router->request()->getRequestData('products');
        $shootingId = \Monkey::app()->router->request()->getRequestData('shootingId');

        foreach ($infos as $info){

            try {
                $product = explode('|', $info)[0];

                $pId = explode('-', $product)[0];
                $pVarId = explode('-', $product)[1];

                $progLineNum = explode('|', $info)[1];

                /** @var CProductHasShooting $phs */
                $phs = \Monkey::app()->repoFactory->create('ProductHasShooting')->findOneBy([
                    'productId'=>$pId,
                    'productVariantId'=>$pVarId,
                    'progressiveLineNumber'=>$progLineNum,
                    'shootingId'=>$shootingId]);

                $phs->lastAztecPrint = date('Y-m-d H:i:s');
                $phs->update();
            } catch (\Throwable $e){
                \Monkey::app()->applicationLog('ProductShootingManage', 'Error', 'Error While updating print date','There was an error while print last QR Print');
            }

        }

        return true;

    }


}