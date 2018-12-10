<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProductDetailLabel;
use bamboo\domain\entities\CProductSheetModelActual;
use bamboo\domain\entities\CProductSheetModelPrototype;
use bamboo\domain\entities\CProductSheetPrototype;
use bamboo\domain\entities\CProductSheetPrototypeHasProductDetailLabel;

/**
 * Class CDetailGetLabelDetailForFind
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 13/07/2018
 * @since 1.0
 */
class CDetailGetLabelDetailForFind extends AAjaxController
{
    public function get()
    {
        $label = \Monkey::app()->router->request()->getRequestData('label');
        $psmpIdsString = \Monkey::app()->router->request()->getRequestData('psmp');

        $idsJon = json_decode($psmpIdsString, true);

        /** @var CRepo $psmpRepo */
        $psmpRepo = \Monkey::app()->repoFactory->create('ProductSheetModelPrototype');

        $res = '';
        foreach ($idsJon as $idJson) {

            foreach ($idJson as $idModel) {
                /** @var CProductSheetModelPrototype $psmp */
                $psmp = $psmpRepo->findOneBy(['id'=>$idModel]);

                /** @var CObjectCollection $prodSheetActual */
                $prodSheetActual = $psmp->productSheetModelActual;



                /** @var CProductSheetModelActual $psma */
                $psma = $prodSheetActual->findOneByKey('productDetailLabelId', $label);

                if(!$psma){
                    $val = "Etichetta non associata a nulla";
                } else {
                    $val = $psma->productDetail->productDetailTranslation->getFirst()->name;
                }

                $res .= '<strong>Modello:</strong> ' . $psmp->code . '| <strong>Valore etichetta:</strong> ' . $val . '<br>';
            }
        }

        return $res;

    }
}