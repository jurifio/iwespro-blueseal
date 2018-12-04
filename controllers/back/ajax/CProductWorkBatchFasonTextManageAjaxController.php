<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\core\utils\slugify\CSlugify;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchTextManage;
use bamboo\domain\entities\CProductSize;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CProductBatchTextManageRepo;
use bamboo\domain\repositories\CProductSizeRepo;


/**
 * Class CProductWorkBatchFasonTextManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/12/2018
 * @since 1.0
 */
class CProductWorkBatchFasonTextManageAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {
        $productBatchId = \Monkey::app()->router->request()->getRequestData('productBatchId');
        $txt = \Monkey::app()->router->request()->getRequestData('txt');

        if(empty($txt)) return 'Non hai inserito nessun testo';

        /** @var CProductBatchTextManage $productBatchTextManage */
        $productBatchTextManage = \Monkey::app()->repoFactory->create('ProductBatch')->findOneBy(['id'=>$productBatchId])->productBatchTextManage;
        $productBatchTextManage->descriptionFason = $txt;
        $productBatchTextManage->update();

        return 'Il testo è stato inserito con successo';
    }


    /**
     * @return string
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put(){

        $type = \Monkey::app()->router->request()->getRequestData('type');

        $productBatchId = \Monkey::app()->router->request()->getRequestData('batchId');

        /** @var CProductBatchTextManageRepo $pbtmr */
        $pbtmr = \Monkey::app()->repoFactory->create('ProductBatchTextManage');

        /** @var CProductBatchTextManage $productBatchTextManage */
        $productBatchTextManage = \Monkey::app()->repoFactory->create('ProductBatch')->findOneBy(['id'=>$productBatchId])->productBatchTextManage;

        if($type == 'fasonOperation'){
            if(!is_null($productBatchTextManage->descriptionFason)){
                $productBatchTextManage->workCategoryStepsId = $pbtmr->goToNextStep($productBatchTextManage->id);
                return 'Lo stato della lavorazione è stato portato a \'COMPLETATO\'. Sei sei sicuro del tuo lavoro puoi completare l\'operazione notificando il termine del lotto cliccando il bottone in alto con la clessidra.';
            } else {
                return 'Non puoi avanzare lo stato della lavorazione se prima non inserisci un testo valido';
            }
        } else if ($type == 'masterOperation'){
            $productBatchTextManage->workCategoryStepsId = \Monkey::app()->router->request()->getRequestData('step');
            $productBatchTextManage->update();
            return 'Modifica avvenuta con successo';
        }

        return true;
    }

}