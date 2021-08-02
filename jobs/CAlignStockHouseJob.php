<?php

namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProductEan;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CShop;
use PDO;
use prepare;
use AEntity;
use bamboo\domain\entities\CSite;
use bamboo\domain\entities\CDirtySkuHasStoreHouse;
use bamboo\domain\entities\CDirtySku;

use PDOException;


class CAlignStockHouseJob extends ACronJob
{

    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function run($args = null)
    {
        $this->AlignQuantity();
    }

    /**
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function AlignQuantity()
    {

        set_time_limit(0);
        ini_set('memory_limit','2048M');
        try {
            $res = "";
            $shopRepo = \Monkey::app()->repoFactory->create('Shop')->findBy(['hasEcommerce' => 1]);
            $dirtySkuRepo = \Monkey::app()->repoFactory->create('DirtySku');
            $storehouseRepo = \Monkey::app()->repoFactory->create('Storehouse');
            $dirtySkuHasStoreHouseRepo = \Monkey::app()->repoFactory->create('dirtySkuHasStoreHouse');
            $dirtySku = $dirtySkuRepo->findAll();
            $this->report('CAlignStockHouseJob','start Sku cycle' . $value->name);
            foreach ($dirtySku as $sku) {
                $dsas = $dirtySkuHasStoreHouseRepo->findOneBy(['dirtySkuId' => $sku->id,'shopId' => $sku->shopId,'storeHouseId' => $sku->storeHouseId]);
                if ($dsas) {
                    $oldQuantity = $dsas->qty;
                    $productCode = $dsas->productId . '-' . $dsas->productVariantId . '-Size: ' . $dsas->size;
                    $dsas->qty = $sku->qty;
                    $dsas->update();
                    if ($oldQuantity != $sku->qty) {
                        $this->report('CAlignStockHouseJob','start update' . $productCode . 'quantity: ' . $sku->qty);
                    }
                } else {
                    continue;
                }
            }
            \Monkey::app()->applicationReport('Finish Procedure CAlignStockHouseJob','End Procedure','','');

        } catch (\Throwable $e) {
            \Monkey::app()->applicationReport('CAlignStockHouseJob','error','Cannot Work',$e->getLine() . '-' . $e->getMessage());
        }


    }

}