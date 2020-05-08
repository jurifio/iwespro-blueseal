<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CMarketplaceHasShopBrandRights;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CProductHasTag;
use bamboo\domain\entities\CTag;
use bamboo\domain\repositories\CPrestashopHasProductRepo;


/**
 * Class CManageMarketplaceHasShopBrandAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 08/05/2020
 * @since 1.0
 */
class CManageMarketplaceHasShopBrandAjaxController extends AAjaxController
{
    /**
     * @return bool
     */
    public function post()
    {
        $marketplaceHasShopBrandRightsRepo = \Monkey::app()->repoFactory->create('MarketplaceHasShopBrandRights');
        /** @var CProductBrand $productBrandRepo */
        $productBrandRepo = \Monkey::app()->repoFactory->create('ProductBrand');
        $data = $this->app->router->request()->getRequestData();
        $brands = $data['brands'];
        $marketplaceShopId = $data['marketplaceShopId'];
        $marketplaceName = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOneBy(['id' => $marketplaceShopId])->name;
        $res = 'Elenco dei brand inseriti per il marketplace ' . $marketplaceName . '<br>';
        foreach ($brands as $brand) {
            $brandName = $productBrandRepo->findOneBy(['id' => $brand])->name;
            /** @var CMarketplaceHasShopBrandRights $findmhsr */
            $findmhsr = $marketplaceHasShopBrandRightsRepo->findOneBy(['marketplaceHasShopId' => $marketplaceShopId,'productBrandId' => $brand]);
            if ($findmhsr != null) {
                $res .= 'Brand ' . $brandName . ' non aggiunto in quanto già presente <br>';
                continue;
            } else {
                /** @var CMarketplaceHasShopBrandRights $mhsr */
                $mhsr = $marketplaceHasShopBrandRightsRepo->getEmptyEntity();
                $mhsr->marketplaceHasShopId = $marketplaceShopId;
                $mhsr->productBrandId = $brand;
                $mhsr->insert();

                $res .= 'Aggiunto il Brand ' . $brandName . '<br>';
            }
        }


        return $res;
    }


}