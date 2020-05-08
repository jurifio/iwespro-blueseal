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
 * Class CManageRulesPriceMarketplaceHasShopAjaxController
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
class CManageRulesPriceMarketplaceHasShopAjaxController extends AAjaxController
{
    /**
     * @return bool
     */
    public function post()
    {
        /** @var CMarketplaceHasShop $marketplaceHasShopRepo **/
        $marketplaceHasShopRepo = \Monkey::app()->repoFactory->create('MarketplaceHasShop');
        $data = $this->app->router->request()->getRequestData();
        $marketplaceShopIds = $data['marketplaceShopIds'];
        $isPriceHub = $data['isPriceHub'];
        if($isPriceHub=='')return 'Devi Selezionare un opzione chiudi e riesegui l\'operazione';

        foreach ($marketplaceShopIds as $marketplaceShopId) {
            $mhs = $marketplaceHasShopRepo->findOneBy(['id' => $marketplaceShopId]);

                /** @var CMarketplaceHasShopBrandRights $mhsr */
                $mhs ->isPriceHub=$isPriceHub;
                $mhs->update();


            }
        $res='Politica Prezzi applicata con successo';


        return $res;
    }


}