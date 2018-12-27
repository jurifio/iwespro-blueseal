<?php

namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CProduct;
use bamboo\domain\repositories\CProductRepo;


/**
 * Class CProductCatalogManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 27/12/2018
 * @since 1.0
 */
class CProductCatalogManageAjaxController extends AAjaxController
{
    public function get(){

        /** @var CProductRepo $pR */
        $pR = \Monkey::app()->repoFactory->create('Product');

        /** @var CProduct $p */
        $p = $pR->findOneByStringId($this->data['prodId']);

        $allInfo = [];

        foreach ($this->data['checkedFields'] as $info){

            switch ($info) {
                case 'sizes':
                    $shopIds = \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
                    $allInfo['sizes'] = $p->getStockSituationTable($shopIds);
                    break;
                case 'externalId':
                    $allInfo['externalId'] = $p->getShopExtenalIds('<br />');
                    break;
                case 'season':
                    $allInfo['season'] = $p->productSeason->name . " " . $p->productSeason->year;
                    break;
                case 'details':
                    $allInfo['details'] = "";
                    foreach ($p->productSheetActual as $k => $v) {
                        if (!is_null($v->productDetail) && !$v->productDetail->productDetailTranslation->isEmpty()) {
                            $allInfo['details'] .= '<span class="small">' . $v->productDetail->productDetailTranslation->getFirst()->name . "</span><br />";
                        }
                    }
                    break;
            }

        }

        return json_encode($allInfo);

    }
}