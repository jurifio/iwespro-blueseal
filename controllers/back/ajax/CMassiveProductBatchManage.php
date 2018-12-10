<?php

namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\repositories\CProductBatchDetailsRepo;
use bamboo\domain\repositories\CProductBatchRepo;


/**
 * Class CMassiveProductBatchManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 21/05/2018
 * @since 1.0
 */
class CMassiveProductBatchManage extends AAjaxController
{

    /**
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     * @throws \bamboo\core\exceptions\RedPandaException
     */
    public function post()
    {
        // ottengo i prodotti e il lotto
        $strProducts = \Monkey::app()->router->request()->getRequestData('products');
        $pBatch = \Monkey::app()->router->request()->getRequestData('batch');
        $option =\Monkey::app()->router->request()->getRequestData('option');

        if(empty($strProducts) || empty($pBatch)) return false;
           //cerco il lotto
        $pbext = \Monkey::app()->repoFactory->create('ProductBatch')->findOneBy(["id"=>$pBatch]);
        // se non lo trovo restituisco falso
        if(is_null($pbext)) return false;

        //inizializzo l'array dei prodotti

        $allProducts = [];
        //converto il testo prodotti stringa in un array
        $productids = explode("\n", $strProducts);

        $not = [];
        $resFinal = [];
        $count = 0;

        foreach ($productids as $id){

            if(empty($id)) continue;

            $pCode = explode('__', $id);

            /** @var CProduct $product */
            $product = \Monkey::app()->repoFactory->create('Product')->findOneByStringId($pCode[0]);

            if(!is_null($product)){

            if ($this->checkAvaiable($product, $option) !== 'ok') {
                $resFinal[$product->printId()] = $this->checkAvaiable($product, $option);
                $count = 0;
                continue;
            }


                $count++;
                $resFinal[$product->printId()] = $pCode[0].'-'.$count;
                $allProducts[] = $pCode[0];
            } else continue;

        }

        /** @var CProductBatchDetailsRepo $pbdRepo */
        $pbdRepo = \Monkey::app()->repoFactory->create('ProductBatchDetails');

        if(!empty($allProducts)){
            $res = $pbdRepo->insertProductInEmptyProductBatch($pBatch, $allProducts);
            if (!$res) return 'Errore durante l\'associazione';
        }


        return json_encode($resFinal);
    }


    /**
     * @param CProduct $product
     * @var $option
     * @return array|bool
     */
    private function checkAvaiable(CProduct $product, $option) {

        $notAvaiable = [];

        if(!$product->hasPhoto()) $notAvaiable['Foto'] = 1;

        if($product->productStatusId == 7 || $product->productStatusId == 8 || $product->productStatusId == 12) $notAvaiable['Stato-'.$product->productStatus->name] = 1;
if ($option=="2") {
    if (is_null($product->productCardPhoto)) $notAvaiable['Scheda_prodotto'] = 1;
}
    if (empty($notAvaiable)) return 'ok';

    return $notAvaiable;
}



    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put(){

        $posProds = \Monkey::app()->router->request()->getRequestData('posProds');
        $note = \Monkey::app()->router->request()->getRequestData('note');
        $type = \Monkey::app()->router->request()->getRequestData('type');

        if(empty($note)) return 'Inserisci il testo della nota';

        /** @var CProductBatchDetailsRepo $pbdRepo */
        $pbdRepo = \Monkey::app()->repoFactory->create('ProductBatchDetails');

        foreach ($posProds as $posProd) {

            /** @var CProductBatchDetails $pbd */
            $pbd = $pbdRepo->findOneBy(['id'=>$posProd]);

            if($type == 's' || is_null($pbd->note)){
                $pbd->note = $note;
            } else if($type == 'a'){
                $pbd->note = $pbd->note.'. '.$note.'.';
            }

            $pbd->workCategoryStepsId = CProductBatchDetails::UNFIT_NORM;
            $pbd->update();

            /** @var CProductBatch $pb */
            $pba = $pbd->productBatch;
            $pba->isFixed = 0;
            $pba->update();
        }

        return 'Note inserite con successo';
    }
}