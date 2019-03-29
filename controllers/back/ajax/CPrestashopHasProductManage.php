<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\domain\entities\CMarketplaceHasShop;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CProductRepo;

/**
 * Class CPrestashopHasProductManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 25/03/2019
 * @since 1.0
 */
class CPrestashopHasProductManage extends AAjaxController
{
    public function get()
    {

        $mhsColl = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findAll();

        $res = [];
        $i = 0;
        /** @var CMarketplaceHasShop $mhs */
        foreach ($mhsColl as $mhs) {
            $res[$i]['id'] = $mhs->id;
            $res[$i]['shop-marketplace'] = $mhs->shop->name . ' | ' . $mhs->marketplace->name;
            $i++;
        }

        return json_encode($res);

    }

    public function post()
    {
        if(empty($this->data['marketplaceHasShopId']) || (empty($this->data['variantValue']) && $this->data['modifyType'] !== 'nf')){
            return 'Inserisci i dati correttamente';
        }

        $mhs = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOneBy(['id' => $this->data['marketplaceHasShopId']]);

        /** @var CProductRepo $productRepo */
        $productRepo = \Monkey::app()->repoFactory->create('Product');

        $products = new CObjectCollection();
        foreach ($this->data['products'] as $productCode) {
            /** @var CProduct $product */
            $product = $productRepo->findOneByStringId($productCode);
            $products->add($product);
        }

        $prestashopProduct = new CPrestashopProduct();
        if ($prestashopProduct->addNewProducts($products, $mhs, $this->data['modifyType'], $this->data['variantValue'])) {
            return 'Prodotti inseriti con successo';
        };

        return 'Errore durante l\'inserimento dei prodotti';
    }

    /**
     * @throws \PrestaShopWebserviceException
     */
    public function put()
    {
        $mhs = \Monkey::app()->repoFactory->create('MarketplaceHasShop')->findOneBy(['id' => $this->data['marketplaceHasShopId']]);

        $prestashopProduct = new CPrestashopProduct();
        if($prestashopProduct->updateProductSaleDescription($this->data['productsPrestashopIds'], $mhs)){
            return 'Saldi aggiornati correttamente';
        };

        return 'Controllare se tutti i saldi sono stati inseriti correttamente';
    }

    public function delete()
    {

    }

}