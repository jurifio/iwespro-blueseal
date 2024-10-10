<?php

namespace bamboo\blueseal\jobs;

use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CMarketplace;
use bamboo\domain\entities\CMarketplaceAccount;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CAggregatorHasProduct;
use bamboo\domain\entities\CMarketplaceAccountHasProduct;
use bamboo\domain\entities\CProductBrand;
use DateTime;
use PDO;


/**
 * Class CAlignShopHasProductFromPrestashopJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 27/04/2020
 * @since 1.0
 */
class CAlignShopHasProductFromPrestashopJob extends ACronJob
{

    /**
     * @param null $args
     */
    public function run($args = null)
    {



        try {
            $this->report('CAlignShopHasProductFromPrestashopJob', 'start connection', '');
            $db_host = '84.247.137.139';
            $db_name = 'cartechini_sco2';
            $db_user = 'cartechini_sco2';
            $db_pass = 'Zora231074!';
            $res = "";
            $shopHasProductRepo = \Monkey::app()->repoFactory->create('ShopHasProduct');
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $res .= " connessione ok <br>";
            } catch (PDOException $e) {
                $res .= $e->getMessage();
                $this->report('Connection to Prestashop DB', 'error connection');
            }
            $arrayProductId=[];
            $stmtCollect = $db_con->prepare('select id_product as prestashopId, reference as productCode from ps_product ');
            $stmtCollect->execute();
            while ($rowCollect = $stmtCollect->fetch(PDO::FETCH_ASSOC)) {
                $productCode=str_replace(' ', '-',trim($rowCollect['productCode']));
                $arrayProductId= explode('-', $productCode);
                $this->report('CAlignShopHasProductFromPrestashopJob', 'product', $rowCollect['productCode'].'-'.$arrayProductId[0].' '.$arrayProductId[1]);
                $shopHasProduct = $shopHasProductRepo->findOneBy(['productId' => $arrayProductId[0], 'productVariantId' => $arrayProductId[1], 'shopId' => $arg]);
                if($shopHasProduct) {
                    $shopHasProduct->prestashopId = $rowCollect['prestashopId'];
                    $shopHasProduct->update();
                }
            }


            $this->report('CAlignShopHasProductFromPrestashopJob', 'End Work Updating', '');
        } catch (\Throwable $e) {
            $this->report('CAlignShopHasProductFromPrestashopJob', 'ERROR Work Updating', $e->getMessage() . '-' . $e->getLine());


        }

    }
}