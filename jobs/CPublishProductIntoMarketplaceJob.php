<?php

namespace bamboo\blueseal\jobs;

use bamboo\domain\entities\CCartAbandonedEmailSend;
use bamboo\domain\repositories\CCartAbandonedEmailSendRepo;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CCart;
use bamboo\domain\entities\CCartAbandonedEmailParam;
use bamboo\domain\entities\CCouponType;
use bamboo\domain\entities\CCoupon;
use bamboo\domain\entities\CCartLine;
use bamboo\core\base\CSerialNumber;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\blueseal\remote\readextdbtable\CReadExtDbTable;
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
                            array_push($rows, [$productSku -> productId . '-' . $product -> productVariantId]);
                        }

                    } else {
                        continue;
                    }
                }

            }
        }
    }
}