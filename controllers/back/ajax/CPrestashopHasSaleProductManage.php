<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\domain\repositories\CPrestashopHasProductRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CProductRepo;

/**
 * Class CPrestashopHasSaleProductManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 02/04/2019
 * @since 1.0
 */
class CPrestashopHasSaleProductManage extends AAjaxController
{
    public function get()
    {
    }

    public function put()
    {
    }

    /**
     * @return string
     * @throws BambooException
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {
        if(empty($this->data['marketplaceHasShopId']) || empty($this->data['modifyType'])) return 'Inserisci i dati correttamente';

        $mhs = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOneBy(['id' => $this->data['marketplaceHasShopId']]);

        $prestashopProduct = new CPrestashopProduct();

        $updateDescription = $this->data['titleModify'] === 'true' ? true : false;
        if($prestashopProduct->insertSpecificPriceForSale($this->data['products'], $mhs, $updateDescription, $this->data['variantValue'], $this->data['modifyType'])){
            return 'Saldi inseriti correttamente';
        };

        return 'Controllare se tutti i saldi sono stati inseriti correttamente';
    }

    /**
     * @return bool
     * @throws BambooException
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function delete()
    {
        $mhs = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOneBy(['id' => $this->data['marketplaceHasShopId']]);
        $prestashopProduct = new CPrestashopProduct();

        /** @var CRepo $phphmhsR */
        $phphmhsR = \Monkey::app()->repoFactory->create('PrestashopHasProductHasMarketplaceHasShop');
        foreach ($this->data['products'] as $product){
            if($prestashopProduct->removeSpecificPriceForSale($product, $mhs)){
                /** @var CPrestashopHasProductHasMarketplaceHasShop $phphmhs */
                $phphmhs = $phphmhsR->findOneBy(['productId' => $product['productId'], 'productVariantId' => $product['productVariantId'], 'marketplaceHasShopId' => $mhs->id]);
                $phphmhs->salePrice = null;
                $phphmhs->isOnSale = 0;
                $phphmhs->update();
            };
        }

        return 'Saldi tolti correttamente';
    }

}