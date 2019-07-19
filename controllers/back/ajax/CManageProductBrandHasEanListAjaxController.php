<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CProductHasTag;
use bamboo\domain\entities\CTag;
use bamboo\domain\repositories\CPrestashopHasProductRepo;


/**
 * Class CManageProductBrandHasEanListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 18/07/2019
 * @since 1.0
 */
class CManageProductBrandHasEanListAjaxController extends AAjaxController
{
    /**
     * @return bool
     */
    public function post()
    {

        $brands = \Monkey::app()->router->request()->getRequestData('brand');
        $hasMarketplaceRights = \Monkey::app()->router->request()->getRequestData('hasMarketplaceRight');
        $hasExternalEan = \Monkey::app()->router->request()->getRequestData('hasExternal');
        $hasAggregator =\Monkey::app()->router->request()->getRequestData('hasAggregator');

        /** @var CProductBrand $productBrandRepo */
        $productBrandRepo=\Monkey::app()->repoFactory->create('ProductBrand');
       foreach ($brands as $brand){
           /** @var CProductBrand $productBrand */
        $productBrand=$productBrandRepo->findOneBy(['id'=>$brand]);
        $productBrand->hasMarketplaceRights=$hasMarketplaceRights;
        $productBrand->hasExternalEan=$hasExternalEan;
        $productBrand->hasAggregator=$hasAggregator;
        $productBrand->update();
       }

        return true;
    }


}