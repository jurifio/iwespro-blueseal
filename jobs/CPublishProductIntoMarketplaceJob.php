<?php

namespace bamboo\blueseal\jobs;


use PDO;
use prepare;

use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProduct;
use bamboo\core\events\AEventListener;
use bamboo\domain\repositories\CMarketplaceAccountHasProductRepo;


/**
 * Class CPublishProductIntoMarketplaceJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/01/2020
 * @since 1.0
 */
class CPublishProductIntoMarketplaceJob extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {

        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        $res = "";
        /********marketplace********/
        $db_host = "5.189.159.187";
        $db_name = "iwesPrestaDB";
        $db_user = "pickyshop4";
        $db_pass = "rrtYvg6W!";
        try {

            $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
            $db_con -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $res .= " connessione ok <br>";
        } catch (PDOException $e) {
            $res .= $e -> getMessage();
        }
        $marketplaceAccountRepo = \Monkey ::app() -> repoFactory -> create('MarketplaceAccount');
        $productBrandRepo = \Monkey ::app() -> repoFactory -> create('ProductBrand');
        $productRepo = \Monkey ::app() -> repoFactory -> create('Product');
        $productSkuRepo = \Monkey ::app() -> repoFactory -> create('ProductSku');

        $marketplaceAccounts = $marketplaceAccountRepo -> findBy(['id' => 32, 'marketplaceId' => 9]);

        foreach ($marketplaceAccounts as $marketplaceAccount) {
            $activeAutomatic = isset($marketplaceAccount -> config['activeAutomatic']) ? $marketplaceAccount -> config['activeAutomatic'] : 0;

            $isActive = isset($marketplaceAccount -> config['isActive']) ? $marketplaceAccount -> config['isActive'] : 0;
            if ($isActive == 0) {
                continue;
            }
            $rows = [];
            $productBrands = [];
            $shops = [];

            $filters = explode(',', json_encode($marketplaceAccount -> config['ruleOption'], false));
            foreach ($filters as $filter) {
                $brandShop = explode('-', $filter);
                $brand = $brandShop[0];
                $shopD = $brandShop[1];
                $products = $productRepo -> findBy(['productBrandId' => $brand, 'productStatusId' => 6]);
                foreach ($products as $product) {
                    if ($product -> qty >= 1) {
                        $productSku = $productSkuRepo -> findOneBy(['productId' => $product -> id, 'productVariantId' => $product -> productVariantId, 'shopId' => $shopD]);
                        if ($productSku != null) {
                            $rows[] = [$productSku->productId . '-' . $product->productVariantId];
                        }

                    } else {
                        continue;
                    }
                }

            }
            /** @var CMarketplaceAccountHasProductRepo $marketplaceAccountHasProductRepo */
            $marketplaceAccountHasProductRepo = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct');
            \Monkey::app()->repoFactory->beginTransaction();
            foreach ($rows as $row) {
                try {
                    $ids = [];
                    set_time_limit(6);
                    $product = $productRepo->findOneByStringId($row);
                    $marketplaceAccountHasProduct = $marketplaceAccountHasProductRepo->addProductToMarketplaceAccountJob($product, $marketplaceAccount,  $activeAutomatic);
                    $i++;
                    \Monkey::app()->repoFactory->commit();
                } catch
                (\Throwable $e) {
                    \Monkey::app()->repoFactory->rollback();
                    throw $e;
                }
            }
        }
    }


}