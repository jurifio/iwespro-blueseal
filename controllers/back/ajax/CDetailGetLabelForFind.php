<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\domain\entities\CProductDetailLabel;
use bamboo\domain\entities\CProductSheetModelActual;
use bamboo\domain\entities\CProductSheetModelPrototype;
use bamboo\domain\entities\CProductSheetPrototype;
use bamboo\domain\entities\CProductSheetPrototypeHasProductDetailLabel;

/**
 * Class CDetailGetLabelForFind
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 12/07/2018
 * @since 1.0
 */
class CDetailGetLabelForFind extends AAjaxController
{
    public function get()
    {
        $pId = \Monkey::app()->router->request()->getRequestData('pid');

        $res = [];

        /** @var CProductSheetPrototype $psp */
        $psp = \Monkey::app()->repoFactory->create('ProductSheetPrototype')->findOneBy(['id' => $pId]);

        /** @var CObjectCollection $detLabCollection */
        $detLabCollection = $psp->productDetailLabel;


        $i = 0;
        /** @var CProductDetailLabel $pDL */
        foreach ($detLabCollection as $pDL) {
            $res[$i]['id'] = $pDL->id;
            $res[$i]['slug'] = $pDL->slug;
            $i++;
        }

        return json_encode($res);
    }
}