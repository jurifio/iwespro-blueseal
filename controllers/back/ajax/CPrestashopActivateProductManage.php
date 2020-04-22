<?php

namespace bamboo\controllers\back\ajax;


use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;

use PDO;

/**
 * Class CPrestashopActivateProductManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 22/04/2020
 * @since 1.0
 */
class CPrestashopActivateProductManage extends AAjaxController
{


    /**
     * @return bool
     * @throws BambooException
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $shop_id = $data['marketplaceHasShopId'];
        $action=$data['action'];

        $prestashopHasProductRepo = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
        $prestashopHasProductHasMarketplaceHasShopRepo = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop');
        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $productPublicSkuRepo = \Monkey::app()->repoFactory->create('ProductPublicSku');

        $mhs = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOneBy(['id' => $shop_id]);
        $prestashopProduct = new CPrestashopProduct();


        /** @var CRepo $phphmhsR */
        $php = \Monkey::app()->repoFactory->create('PrestashopHasProduct');
        foreach ($data['rows'] as $collectionProduct) {
            $dataProduct=explode('-',$collectionProduct);
           // $product=$phphmhsR->findOneBy(['productId'=>$dataProduct[0],'productVariantId'=>$dataProduct[1]]);
            $product=$php->findOneBy(['productId'=>109229,'productVariantId'=>4626373]);
           $prestashopProduct->activateProduct($product, $mhs,$action);

        }

        return 'Prodotto Riattivato';
    }

}