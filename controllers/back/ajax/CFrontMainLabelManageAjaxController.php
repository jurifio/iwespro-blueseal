<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductDetailLabel;
use bamboo\domain\entities\CProductSheetActual;
use bamboo\domain\entities\CProductSheetPrototype;
use bamboo\domain\repositories\CProductRepo;

/**
 * Class CFrontMainLabelManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 27/06/2018
 * @since 1.0
 */
class CFrontMainLabelManageAjaxController extends AAjaxController
{
    public function get(){

        $protId = \Monkey::app()->router->request()->getRequestData('prototypeId');

        /** @var CProductSheetPrototype $pSheetPrototype */
        $pSheetPrototype = \Monkey::app()->repoFactory->create('ProductSheetPrototype')->findOneBy(['id'=>$protId]);

        /** @var CObjectCollection $prodDetailLabel */
        $prodDetailLabels = $pSheetPrototype->productDetailLabel;

        $i = 0;
        $labels = [];
        /** @var CProductDetailLabel $detailLabel */
        foreach ($prodDetailLabels as $detailLabel){
            if(strpos($detailLabel->slug, 'etichetta') !== false){
                $labels[$i]['id'] = $detailLabel->id;
                $labels[$i]['slug'] = $detailLabel->slug;
                $labels[$i]['name'] = $detailLabel->productDetailLabelTranslation->findOneByKey('langId',1)->name;
                $i++;
            }
        }

        return json_encode($labels);

    }


    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function put() {

        $prodIds = \Monkey::app()->router->request()->getRequestData('products');
        $labelValue = \Monkey::app()->router->request()->getRequestData('labelValue');

        /** @var CRepo $pSActualRepo */
        $pSActualRepo = \Monkey::app()->repoFactory->create('ProductSheetActual');

        try {
            \Monkey::app()->repoFactory->beginTransaction();
            foreach ($prodIds as $prodId) {

                foreach ($labelValue as $val) {
                    foreach ($val as $key => $value) {

                        $exPSAc = $pSActualRepo->findOneBy([
                            'productId' => explode('-', $prodId)[0],
                            'productVariantId' => explode('-', $prodId)[1],
                            'productDetailLabelId' => $key,
                        ]);

                        if (!$exPSAc) {
                            /** @var CProductSheetActual $pSheetActual */
                            $pSheetActual = $pSActualRepo->getEmptyEntity();
                            $pSheetActual->productId = explode('-', $prodId)[0];
                            $pSheetActual->productVariantId = explode('-', $prodId)[1];
                            $pSheetActual->productDetailLabelId = $key;
                            $pSheetActual->productDetailId = $value;
                            $pSheetActual->smartInsert();
                        }
                    }
                }
            }
            \Monkey::app()->repoFactory->commit();
        } catch (\Throwable $e){
            \Monkey::app()->repoFactory->rollback();
        }

        return 'Etichette aggiunte con successo';

    }

}