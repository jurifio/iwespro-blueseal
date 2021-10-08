<?php

namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProductEan;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CShop;
use PDO;
use PDOException;

/**
 * Class CAlignQuantityJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 08/10/2021
 * @since 1.0
 */
class CAlignQuantityJob extends ACronJob
{

    /**
     * @param null $args
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function run($args = null)
    {
        $this->alignProduct();
    }

    /**
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function alignProduct()
    {
        $res = "";
        try {
            $this->report('Start Align Quantity  in  Iwes  ','Shop To Update all');
            $db_host = "localhost";
            $db_name = "pickyshopfront";
            $db_user = "root";
            $db_pass = "fGLyZV4N3vapUo9";
            $res = "";
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                $res .= " connessione ok <br>";
            } catch (PDOException $e) {
                $res .= $e->getMessage();
            }
            $productRepo = \Monkey::app()->repoFactory->create('Product');
            $sql = 'SELECT ps.productId as productId, ps.productVariantId as productVariantId, ps.stockQty  AS qty from ProductSku ps
WHERE ps.stockQty > 0
GROUP BY ps.productId, ps.productVariantId Order BY ps.productId desc';
            $res = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
            foreach ($res as $result) {
                $productId = $result['productId'];
                $productVariantId = $result['productVariantId'];
                $qty = $result['qty'];
                $product = $productRepo->findOneBy(['id' => $productId,'productVariantId' => $productVariantId]);
                $product->qty = $qty;
                $product->update();
            }
        } catch (\Throwable $e) {
            $this->report('CAlignQuantitJob',$e->getMessage());
        }


    }

}