<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CProductBatchHasProductBrand;
use bamboo\domain\entities\CWorkCategory;
use bamboo\domain\repositories\CWorkCategoryRepo;
use bamboo\domain\repositories\CWorkCategoryStepsRepo;

/**
 * Class CEmptyProductBrandBatchManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/06/2018
 * @since 1.0
 */
class CEmptyProductBrandBatchManage extends AAjaxController
{

    public function post()
    {
       $batchId = \Monkey::app()->router->request()->getRequestData('batch');
       $brandIds = \Monkey::app()->router->request()->getRequestData('brand');

       if(empty($batchId)) return 'Inserisci un lotto valido';

       /** @var CRepo $phpbRepo */
       $phpbRepo = \Monkey::app()->repoFactory->create('ProductBatchHasProductBrand');

       /** @var CWorkCategoryStepsRepo $wksr */
        $wksr = \Monkey::app()->repoFactory->create('WorkCategorySteps');

       foreach ($brandIds as $brandId){

           /** @var CProductBatchHasProductBrand $ext */
           $ext = $phpbRepo->findOneBy(['productBatchId'=>$batchId, 'productBrandId'=>$brandId]);

           if(!is_null($ext)) continue;

           /** @var CProductBatchHasProductBrand $phpb */
           $phpb = $phpbRepo->getEmptyEntity();
           $phpb->productBatchId = $batchId;
           $phpb->productBrandId = $brandId;
           $phpb->workCategoryStepsId = $wksr->getFirstStepsFromCategoryId(CWorkCategory::BRAND)->id;
           $phpb->smartInsert();
       }

       return 'Prodotti associati con successo';

    }

    public function delete(){
        $brands = \Monkey::app()->router->request()->getRequestData('brands');
        $productBatchId = \Monkey::app()->router->request()->getRequestData('productBatchId');
        $emptyBatch = \Monkey::app()->router->request()->getRequestData('emptyBatch');

        $e = ($emptyBatch == 'empty' ? true : false);

        /** @var CRepo $pbhpbRepo */
        $pbhpbRepo = \Monkey::app()->repoFactory->create('ProductBatchHasProductBrand');

        if($e){

            foreach ($brands as $brand) {
                /** @var CProductBatchHasProductBrand $pbhpb */
                $pbhpb = $pbhpbRepo->findOneBy(['productBatchId' => $productBatchId,
                    'productBrandId' =>$brand]);
                $pbhpb->delete();
            }
        }

        return 'Brand eliminati con successo dal lotto';
    }


    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put(){
        $brands = \Monkey::app()->router->request()->getRequestData('ids');
        $batch = \Monkey::app()->router->request()->getRequestData('batch');

        /** @var CRepo $pbhpbRepo */
        $pbhpbRepo = \Monkey::app()->repoFactory->create('ProductBatchHasProductBrand');

        foreach ($brands as $brand){
            /** @var CProductBatchHasProductBrand $pbhpb */
            $pbhpb = $pbhpbRepo->findOneBy(['productBatchId'=>$batch, 'productBrandId'=>$brand]);

            $catToChange = $pbhpb->workCategoryStepsId;

            if(!is_null($pbhpb->workCategorySteps->rgt)) {
                $pbhpb->workCategoryStepsId = $pbhpb->workCategorySteps->rgt;
                $pbhpb->update();
            }


            if($catToChange == CProductBatchHasProductBrand::UNFIT_BRAND){


                /** @var CProductBatch $pb */
                $pb = $pbhpb->productBatch;

                if($pb->isValid() == 'ok'){
                    $pb->isFixed = 1;
                    $pb->unfitDate = date('Y-m-d H:i:s');
                    $pb->update();
                }
            }
        }


        return 'Steps aggiornati con successo';

    }

}